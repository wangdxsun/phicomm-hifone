<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:37
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Question\AddQuestionCommand;
use Hifone\Models\Question;
use Auth;
use Hifone\Models\User;
use DB;

class QuestionBll extends BaseBll
{
    public function questions($tagId)
    {
        if ($tagId) {
            $questions = Question::with(['user', 'tags'])->ofTag($tagId)->recent()->paginate();
        } else {
            $questions = Question::with(['user', 'tags'])->orderBy('order', 'desc')->recent()->paginate();
        }

        return $questions;
    }

    public function showQuestion(Question $question)
    {
        $question = $question->load(['user', 'tags']);
        $question->followed = Auth::check() ? Auth::user()->hasFollowQuestion($question) : false;
        $question->user->followed = Auth::check()? User::hasFollowUser($question->user) : false;
        $question->reported = Auth::check() ? Auth::user()->hasReportQuestion($question) : false;

        return $question;
    }

    public function createQuestion($questionData)
    {
        DB::beginTransaction();
        try {
            $question = dispatch(new AddQuestionCommand(
                $questionData['title'],
                $questionData['body'],
                $questionData['tagIds'],
                $questionData['score'],
                Auth::id(),
                get_request_agent(),
                getClientIp()
            ));
            if ($this->needNoAudit($question)) {
                $this->autoAudit($question);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        $question = Question::find($question->id)->load(['user', 'tags']);

        return $question;
    }

    public function autoAudit(Question $question)
    {
        // todo 触发各类事件
        $question->status = Question::VISIBLE;
        $this->updateOpLog($question, '自动审核通过');
        $question->user->update(['question_count' => $question->user->questions()->visibleAndDeleted()->count()]);
    }

    public function needNoAudit(Question $question)
    {
        return !$this->hasVideo($question->body) && !$this->hasUrl($question->body) && !$this->hasImage($question->body) && $question->bad_word === '';
    }
}