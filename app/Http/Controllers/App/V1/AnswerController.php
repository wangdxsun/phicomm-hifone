<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Controllers\App\V1;

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

}