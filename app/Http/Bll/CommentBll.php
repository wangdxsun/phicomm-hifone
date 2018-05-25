<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:04
 */

namespace Hifone\Http\Bll;

use Carbon\Carbon;
use Hifone\Commands\Comment\AddCommentCommand;
use Hifone\Events\Comment\CommentedWasAddedEvent;
use Hifone\Exceptions\Consts\AnswerEx;
use Hifone\Exceptions\Consts\CommentEx;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use DB;
use Auth;

class CommentBll extends BaseBll
{
    public function createComment($commentData)
    {
        DB::beginTransaction();
        try {
            $comment = dispatch(new AddCommentCommand(
                $commentData['body'],
                Auth::id(),
                $commentData['answer_id'],
                array_get($commentData, 'comment_id'),
                get_request_agent(),
                getClientIp()
            ));
            if ($this->needNoAudit($comment)) {
                $this->autoAudit($comment);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        $comment = Comment::find($comment->id)->load(['user', 'comment.user']);

        return $comment;
    }

    public function autoAudit(Comment $comment)
    {
        $comment->status = Comment::VISIBLE;
        $this->updateOpLog($comment, '自动审核通过');
        $comment->user->update(['comment_count' => $comment->user->comments()->visibleAndDeleted()->count()]);
        $comment->answer->update([
            'comment_count' => $comment->answer->comments()->visibleAndDeleted()->count(),
            'last_comment_time' => Carbon::now()->toDateTimeString()
        ]);

        event(new CommentedWasAddedEvent($comment->user, $comment->answer));
    }

    public function needNoAudit(Comment $comment)
    {
        return !$this->hasVideo($comment->body) && !$this->hasUrl($comment->body) && !$this->hasImage($comment->body) && $comment->bad_word === '';
    }

    public function checkComment($commentId)
    {
        if (!empty($commentId)) {
            $comment = Comment::find($commentId);
            if (is_null($comment)) {
                throw new HifoneException('回复不存在', CommentEx::NOT_EXISTED);
            } elseif ($comment->status <> Comment::VISIBLE) {
                throw new HifoneException('该回复已被删除', CommentEx::DELETED);
            }
        }
    }

    public function checkAnswer($answerId)
    {
        $answer = Answer::find($answerId);
        if (is_null($answer)) {
            throw new HifoneException('回答不存在', AnswerEx::NOT_EXISTED);
        } elseif ($answer->status <> Answer::VISIBLE) {
            throw new HifoneException('该回答已被删除', AnswerEx::DELETED);
        }
        (new AnswerBll)->checkQuestion($answer->question_id);
    }

}