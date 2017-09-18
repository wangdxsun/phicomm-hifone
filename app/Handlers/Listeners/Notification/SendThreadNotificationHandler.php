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
use Hifone\Events\Thread\ThreadEventInterface;
use Hifone\Models\Thread;
use Hifone\Models\User;

class SendThreadNotificationHandler
{
    /**
     * Handle the thread.
     */
    public function handle(ThreadEventInterface $event)
    {
        $this->trigger($event->thread);
    }

    protected function trigger(Thread &$thread)
    {
        $this->newThreadNotify($thread);
    }

    protected function newThreadNotify(Thread $thread)
    {
        // Notify followed users
        foreach($thread->user->followers()->get() as $followers)
        {
            if(empty($followers->user())) {
                continue;
            }
            $followers->user()->increment('notification_follow_count',1);
        }
        app('notifier')->batchNotify(
            'followed_user_new_thread',
            $thread->user,
            $thread->user->followers()->get(),
            $thread
        );
        // Notify mentioned users
        $parserAt = app('parser.at');
        $parserAt->parse($thread->body_original);
        foreach($parserAt->users as $users)
        {
            $users->increment('notification_at_count',1);
        }


        app('notifier')->batchNotify(
            'thread_mention',
            $thread->user,
            $parserAt->users,
            $thread
        );
    }
}
