<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 8:37
 */

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Exceptions\Consts\AnswerEx;
use Hifone\Exceptions\Consts\CommentEx;
use Hifone\Exceptions\Consts\QuestionEx;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Question;
use Hifone\Models\Reply;
use Hifone\Models\Report;
use Hifone\Models\Thread;
use Input;

class ReportBll
{
    public function reportThread(Thread $thread)
    {
        if ($thread->status <> Thread::VISIBLE) {
            throw new HifoneException('该帖子已被删除', 410);
        }
        if (Auth::id() === $thread->user->id) {
            throw new HifoneException('自己不能举报自己哦');
        }
        if (Auth::user()->hasReportThread($thread)) {
            throw new HifoneException('已举报');
        }
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();

        $thread->reports()->create($reportData);
    }

    public function reportReply(Reply $reply)
    {
        if ($reply->status <> Reply::VISIBLE) {
            throw new HifoneException('该评论已被删除');
        }
        if (Auth::id() === $reply->user->id) {
            throw new HifoneException('自己不能举报自己哦');
        }
        if (Auth::user()->hasReportReply($reply)) {
            throw new HifoneException('已举报');
        }
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();

        $reply->reports()->create($reportData);
    }

    public function reportQuestion(Question $question)
    {
        if ($question->status <> Question::VISIBLE) {
            throw new HifoneException('该问答已被删除', QuestionEx::DELETED);
        }
        if (Auth::id() === $question->user->id) {
            throw new HifoneException('自己不能举报自己哦');
        }
        if (Auth::user()->hasReportQuestion($question)) {
            throw new HifoneException('已举报');
        }
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();

        $question->reports()->create($reportData);
    }

    public function reportAnswer(Answer $answer)
    {
        if ($answer->status <> Answer::VISIBLE) {
            throw new HifoneException('该回答已被删除', AnswerEx::DELETED);
        }
        if ($answer->question->status <> Question::VISIBLE) {
            throw new HifoneException('该问答已被删除', QuestionEx::DELETED);
        }
        if (Auth::id() === $answer->user->id) {
            throw new HifoneException('自己不能举报自己哦');
        }
        if (Auth::user()->hasReportAnswer($answer)) {
            throw new HifoneException('已举报');
        }
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();

        $answer->reports()->create($reportData);
    }

    public function reportComment(Comment $comment)
    {
        if ($comment->status <> Comment::VISIBLE) {
            throw new HifoneException('该回复已被删除', CommentEx::DELETED);
        }
        if ($comment->answer->status <> Answer::VISIBLE) {
            throw new HifoneException('该回答已被删除', AnswerEx::DELETED);
        }
        if ($comment->answer->question->status <> Question::VISIBLE) {
            throw new HifoneException('该问答已被删除', QuestionEx::DELETED);
        }
        if (Auth::id() === $comment->user->id) {
            throw new HifoneException('自己不能举报自己哦');
        }
        if (Auth::user()->hasReportComment($comment)) {
            throw new HifoneException('已举报');
        }
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();

        $comment->reports()->create($reportData);
    }

    public function getReason()
    {
        return Report::$reason;
    }
}