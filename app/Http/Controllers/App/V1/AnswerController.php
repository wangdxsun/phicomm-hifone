<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Controllers\App\V1;

use Carbon\Carbon;
use Hifone\Exceptions\Consts\AnswerEx;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\AnswerBll;
use Hifone\Http\Bll\CommentBll;
use Hifone\Http\Controllers\App\AppController;
use Auth;
use Hifone\Models\Answer;
use Hifone\Models\Question;
use Hifone\Models\User;

class AnswerController extends AppController
{
    public function store(AnswerBll $answerBll)
    {
        $answerBll->checkPermission(Auth::user());
        $answerBll->checkQuestion(request('question_id'));
        //App图文混排
        $bodies = json_decode(request('body'), true);
        $content = $this->makeMixedContent($bodies);

        $wordCount = mb_strlen(strip_tags($content));
        if ($wordCount < 5 || $wordCount > 800) {
            throw new HifoneException('请输入内容5~800个字');
        }

        $answerData = [
            'body' => $content,
            'question_id' => request('question_id')
        ];
        $answer = $answerBll->createAnswer($answerData);

        return $answer;
    }

    public function show(Answer $answer, AnswerBll $answerBll)
    {
        return $answerBll->showAnswer($answer);
    }

    public function comments(Answer $answer, AnswerBll $answerBll, CommentBll $commentBll)
    {
        $answerBll->checkQuestion($answer->question_id);
        $commentBll->checkAnswer($answer->id);

        return $answerBll->sortComments($answer);
    }

    //邀请回答
    public function invite(User $user, Question $question, AnswerBll $answerBll)
    {
        $answerBll->checkQuestion($question->id);
        //todo 24小时内最多邀请15人

        //被邀请用户已被禁言
        $answerBll->checkPermission($user);
        //被邀请用户已回答
        if ($user->hasAnswerQuestion($question)) {
            throw new HifoneException('已回答', AnswerEx::HAS_ANSWERED);
        }
        //用户已被邀请（唯一索引异常cover）
        $answerBll->createInvite($user, $question);

        return success('已邀请');
    }

    //采纳回答
    /**
     * 提问者，在第一个回答者回答算起的5天内可以采纳任意一个回答，采纳后，悬赏的分值会给到该回答用户；
     * 若5天后，用户没有操作，以此为时间算起，10天后管理员未处理含管理员选不出最佳答案，系统将悬赏分值给到点赞数最高的用户，
     * 同等的点赞数给到第一个回答用户（主要也是为了兼顾以后不用干预的处理，同时也是刺激用户参与回答）；
     * @param Answer $answer
     * @param AnswerBll $answerBll
     * @throws HifoneException
     * @return String
     */
    public function adopt(Answer $answer, AnswerBll $answerBll)
    {
        if (Auth::id() <> $answer->question->user_id) {
            throw new HifoneException('非问题作者不能采纳');
        } elseif (Auth::id() == $answer->user_id) {
            throw new HifoneException('不能采纳自己的回答');
        }
        //按照时间执行自己、管理员采纳操作和系统自动采纳、提醒用户采纳
        if (Carbon::now()->subHours(24*5)->format('Y-m-d H:i') > $answer->question->first_answer_time) {
            throw new HifoneException('采纳有效期已过');
        }
        $answerBll->adoptAnswer($answer);

        return success('已采纳');
    }

}