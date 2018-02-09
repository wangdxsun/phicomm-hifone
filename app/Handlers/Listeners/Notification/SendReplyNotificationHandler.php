<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Listeners\Notification;

use Hifone\Events\Reply\ReplyEventInterface;
use Hifone\Models\Reply;

class SendReplyNotificationHandler
{
    public function handle(ReplyEventInterface $event)
    {
        $this->newReplyNotify($event->reply);
    }

    protected function newReplyNotify(Reply $reply)
    {
        $thread = $reply->thread;
        // Notify the author
        app('notifier')->notify(
            'thread_new_reply',
            $reply->user,
            $thread->user,
            $reply,
            $reply->body
        );

        //通知被回复的回复作者
        if (!empty($reply->reply_id) && $reply->user_id != $reply->reply->user_id) {
            app('notifier')->notify('reply_reply', $reply->user, $reply->reply->user, $reply);
        }

        // Notify followed users
        app('notifier')->batchNotify(
            'followed_thread_new_reply',
            $reply->user,
            $thread->followers()->get(),
            $reply->thread,
            $reply->body
        );

        $parserAt = app('parser.at');
        $parserAt->parse($reply->body_original);

        // Notify mentioned users
        app('notifier')->batchNotify(
            'reply_mention',
            $reply->user,
            $parserAt->users,
            $reply,
            $reply->body
        );
    }
}
