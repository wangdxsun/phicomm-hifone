<?php
namespace Hifone\Handlers\Listeners\Notification;

use Hifone\Events\Comment\CommentWasAuditedEvent;
use Hifone\Models\Comment;

//发表回复之后通知：通知被回复者，通知@的人
class SendCommentNotificationHandler
{
    public function handle(CommentWasAuditedEvent $event)
    {
        $this->newCommentNotify($event->comment);
    }

    public function newCommentNotify(Comment $comment)
    {
        if (null ==  $comment->comment_id) {
            //回答被评论
            app('notifier')->notify(
                'answer_new_comment',
                $comment->user,
                $comment->answer->user,
                $comment,
                $comment->body
            );
        } else {
            //评论被回复
            app('notifier')->notify(
                'comment_new_comment',
                $comment->user,
                $comment->comment->user,
                $comment,
                $comment->body
            );
        }

        //通知@的人
        $parserAt = app('parser.at');
        $parserAt->parse($comment->body_original);

        // Notify mentioned users
        app('notifier')->batchNotify(
            'comment_mention',
            $comment->user,
            $parserAt->users,
            $comment,
            $comment->body
        );

    }
}