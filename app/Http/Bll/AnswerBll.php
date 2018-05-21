<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Bll;

use Carbon\Carbon;
use Hifone\Commands\Answer\AddAnswerCommand;
use Hifone\Events\Answer\AnswerWasAuditedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Answer;
use Hifone\Models\Question;
use Hifone\Models\User;
use Illuminate\Support\Facades\DB;
use Auth;

class AnswerBll extends BaseBll
{
    public function search($keyword)
    {
        $answers = Answer::searchAnswer($keyword)->load(['user', 'question'])->paginate(15);

        return $answers;
    }

    public function showAnswer(Answer $answer)
    {
        //判断问题状态后再显示回答详情
        $this->checkQuestion($answer->question_id);
        $answer = $answer->load(['user', 'question']);
        $answer->user['followed'] = Auth::check() ? User::hasFollowUser($answer->user) : false ;
        $answer['reported'] = Auth::check() ? Auth::user()->hasReportAnswer($answer) : false;

        return $answer;
    }

    public function createAnswer($answerData)
    {
        DB::beginTransaction();
        try {
            $answer = dispatch(new AddAnswerCommand(
                $answerData['body'],
                Auth::id(),
                $answerData['question_id'],
                get_request_agent(),
                getClientIp()
            ));

            if ($this->needNoAudit($answer)) {
                $this->autoAudit($answer);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        $answer = Answer::find($answer->id)->load(['user', 'question.user', 'question.tags']);

        return $answer;
    }

    public function autoAudit(Answer $answer)
    {
        $answer->status = Answer::VISIBLE;
        $this->updateOpLog($answer, '自动审核通过');
        $answer->user->update(['answer_count' => $answer->user->answers()->visibleAndDeleted()->count()]);
        $answer->question->update([
            'answer_count' => $answer->question->answers()->visibleAndDeleted()->count(),
            'last_answer_time' => Carbon::now()->toDateTimeString()
        ]);

        //回答审核通过，加经验值，更新关注人新通知数
        if($answer->user->id != $answer->question->user->id) {
            event(new AnswerWasAuditedEvent($answer->user, $answer));
        }
    }

    public function needNoAudit(Answer $answer)
    {
        return !$this->hasVideo($answer->body) && !$this->hasUrl($answer->body) && !$this->hasImage($answer->body) && $answer->bad_word === '';
    }

    public function checkQuestion($questionId)
    {
        $question = Question::find($questionId);
        if (is_null($question)) {
            throw new HifoneException('问答不存在');
        } elseif ($question->status <> Question::VISIBLE) {
            throw new HifoneException('该问答已被删除');
        }
    }

    public function checkPermission()
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('你已被禁言');
        } elseif (Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
    }


}