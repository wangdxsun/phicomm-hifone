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
use Hifone\Events\Credit\CreditWasAddedEvent;
use Hifone\Events\EventInterface;
use Hifone\Events\Favorite\FavoriteEventInterface;
use Hifone\Events\Follow\FollowedWasAddedEvent;
use Hifone\Events\Follow\FollowEventInterface;
use Hifone\Events\Like\LikeEventInterface;
use Hifone\Events\Thread\ThreadWasMarkedExcellentEvent;
use Hifone\Events\Thread\ThreadWasMovedEvent;
use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Events\User\UserWasLoggedinEvent;
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Models\Thread;
use Hifone\Events\Thread\ThreadWasPinnedEvent;
use Hifone\Events\Thread\ThreadWasLikedEvent;
class SendSingleNotificationHandler
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event)
    {
        // follow
        if ($event instanceof FollowedWasAddedEvent) {
            $this->follow($event->target);
        } elseif ($event instanceof ThreadWasLikedEvent) {
            //like oneself's thread or reply ,there is no notification.
            if (Auth::user() != $event->target->user){
                $this->like($event->target);
            }
        } elseif ($event instanceof FavoriteWasAddedEvent) {
            $this->favorite($event->thread);
        } elseif ($event instanceof ThreadWasMarkedExcellentEvent) {
            $this->markedExcellent($event->target);
        } elseif ($event instanceof ThreadWasMovedEvent) {
            $this->movedThread($event->target);
        } elseif ($event instanceof ThreadWasPinnedEvent){
            $this->threadPinned($event->target);
        }
    }

    protected function follow($target)
    {
        $type = ($target instanceof Thread) ? 'thread_follow' : 'user_follow';

        if ($type == 'thread_follow') {
            app('notifier')->notify($type, Auth::user(), $target->user, $target);
        } else {
            app('notifier')->notify($type, Auth::user(), $target, $target);
        }
    }

    protected function like($target)
    {
        $type = ($target instanceof Thread) ? 'thread_like' : 'reply_like';
        if ($type == 'reply_like'){
            $target = $target->thread;
        }
        app('notifier')->notify($type, Auth::user(), $target->user, $target);
    }

    protected function favorite($thread)
    {
        app('notifier')->notify('thread_favorite', Auth::user(), $thread->user, $thread);
    }

    protected function markedExcellent($target)
    {
        app('notifier')->notify('thread_mark_excellent', Auth::user(), $target->user, $target);
    }

    protected function movedThread($target)
    {
        app('notifier')->notify('thread_move', Auth::user(), $target->user, $target);
    }

    protected function notifyCredit($action, $user, $credit)
    {
        app('notifier')->notify($action, $user, $user, $credit);
    }
    protected function threadPinned($target)
    {
        app('notifier')->notify('thread_pin', Auth::user(), $target->user, $target);
    }
}
