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
        $thread->user->followers()->chunk(100, function ($followers) use ($thread) {
            app('notifier')->batchNotify(
                'followed_user_new_thread',
                $thread->user,
                $followers,
                $thread
            );
        });

        // Notify mentioned users
        $parserAt = app('parser.at');
        $parserAt->parse($thread->body_original);
        app('notifier')->batchNotify(
            'thread_mention',
            $thread->user,
            $parserAt->users,
            $thread
        );
    }
}
