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

use Auth;
use Hifone\Events\Reply\ReplyEventInterface;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;

class SendReplyNotificationHandler
{
    /**
     * Handle the thread.
     */
    public function handle(ReplyEventInterface $event)
    {
        $this->newReplyNotify($event->reply);
    }

    protected function newReplyNotify(Reply $reply)
    {
        $thread = $reply->thread;
        if($reply->user->id != $thread->user->id)
        {
            $thread->user()->increment('notification_reply_count', 1);
        }
        // Notify the author
        app('notifier')->batchNotify(
            'thread_new_reply',
            $reply->user,
            [$thread->user],
            $reply,
            $reply->body
        );

        // Notify followed users
        app('notifier')->batchNotify(
            'followed_thread_new_reply',
            $reply->user,
            $thread->followers()->get(),
            $reply->thread,
            $reply->body
        );
        foreach($thread->followers()->get() as $followers)
        {
            $followers->user()->increment('notification_follow_count',1);
        }

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
        foreach($parserAt->users as $users)
        {
            if($reply->user->id != $users->id)
            {
                $users->increment('notification_at_count',1);
            }
        }
    }
}
