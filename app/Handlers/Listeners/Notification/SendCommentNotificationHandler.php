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
        //通知被回复的人
        app('notifier')->notify(
            'comment',
            $comment->user,
            $comment->comment->user or $comment->answer->user,
            $comment,
            $comment->body
        );

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