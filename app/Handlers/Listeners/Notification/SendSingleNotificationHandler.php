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
    public function handle(EventInterface $event)
    {
        // follow
        if ($event instanceof FollowedWasAddedEvent) {
            $this->follow($event->target);
        } elseif ($event instanceof ThreadWasLikedEvent) {
            $this->like($event->target);
        } elseif ($event instanceof FavoriteWasAddedEvent) {
            $this->favorite($event->thread);
        } elseif ($event instanceof ThreadWasMarkedExcellentEvent) {
            $this->markedExcellent($event->target);
        } elseif ($event instanceof ThreadWasMovedEvent) {
            $this->movedThread($event->target);
        } elseif ($event instanceof ThreadWasPinnedEvent){
            $this->threadPinned($event->target);
        }
        elseif ($event instanceof CreditWasAddedEvent) {
//            if ($event->upstream_event instanceof UserWasAddedEvent) {
//                $this->notifyCredit('credit_register', $event->upstream_event->user, $event->credit);
//            } elseif ($event->upstream_event instanceof UserWasLoggedinEvent) {
//                $this->notifyCredit('credit_login', $event->upstream_event->user, $event->credit);
//            }elseif ($event->upstream_event instanceof FavoriteWasAddedEvent) {
//                $this->notifyCredit('credit_favorite', $event->upstream_event->user, $event->credit);
//            } else {
//                return;
//            }
        }
    }

    protected function follow($target)
    {
        $type = ($target instanceof Thread) ? 'thread_follow' : 'user_follow';

        if ($type == 'thread_follow') {
            app('notifier')->notify($type, Auth::user(), $target->user, $target);
            $target->user->increment('notification_system_count', 1);
        } else {
            app('notifier')->notify($type, Auth::user(), $target, $target);
            $target->increment('notification_system_count', 1);
        }
    }

    protected function like($target)
    {
        $type = ($target instanceof Thread) ? 'thread_like' : 'reply_like';
        app('notifier')->notify($type, Auth::user(), $target->user, $target);
        $target->user->increment('notification_system_count', 1);
    }

    protected function favorite($thread)
    {
        app('notifier')->notify('thread_favorite', Auth::user(), $thread->user, $thread);
        $thread->user->increment('notification_system_count', 1);
    }

    protected function markedExcellent($target)
    {
        app('notifier')->notify('thread_mark_excellent', Auth::user(), $target->user, $target);
        $target->user->increment('notification_system_count', 1);
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
        $target->user->increment('notification_system_count', 1);
    }
}
