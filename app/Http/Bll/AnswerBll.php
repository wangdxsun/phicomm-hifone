<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:01
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Answer\AddAnswerCommand;
use Hifone\Models\Answer;
use Illuminate\Support\Facades\DB;
use Auth;

class AnswerBll extends BaseBll
{
    public function search($keyword)
    {
        $answers = Answer::searchAnswer($keyword)->load(['user', 'question'])->paginate(15);

        return $answers;
    }

    public function createAnswer($answerData)
    {
        DB::beginTransaction();
        try {
            $answer = dispatch(new AddAnswerCommand(
                $answerData['body'],
                $answerData['question_id'],
                Auth::id(),
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
        // todo 触发各类事件
        $answer->status = Answer::VISIBLE;
        $this->updateOpLog($answer, '自动审核通过');
        $answer->user->update(['answer_count' => $answer->user->answers()->visibleAndDeleted()->count()]);
    }

    public function needNoAudit(Answer $answer)
    {
        return !$this->hasVideo($answer->body) && !$this->hasUrl($answer->body) && !$this->hasImage($answer->body) && $answer->bad_word === '';
    }
}