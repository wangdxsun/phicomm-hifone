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
        \Log::info('before notify');
        $thread = $reply->thread;
        // Notify the author
        app('notifier')->batchNotify(
            'thread_new_reply',
            $reply->user,
            [$thread->user],
            $reply,
            $reply->body
        );
        \Log::info('after notify');

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
