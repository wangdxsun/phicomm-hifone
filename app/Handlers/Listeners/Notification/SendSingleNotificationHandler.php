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
use Hifone\Events\EventInterface;
use Hifone\Events\Follow\FollowedWasAddedEvent;
use Hifone\Events\Like\LikedWasAddedEvent;
use Hifone\Events\Reply\ReplyWasPinnedEvent;
use Hifone\Events\Thread\ThreadWasMarkedExcellentEvent;
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Events\Thread\ThreadWasPinnedEvent;
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
        } elseif ($event instanceof LikedWasAddedEvent) {
            //like oneself's thread or reply ,there is no notification.
            if (Auth::user()->id != $event->user->id){
                $this->like($event->user);
            }
        } elseif ($event instanceof FavoriteWasAddedEvent) {
            $this->favorite($event->thread);
        } elseif ($event instanceof ThreadWasMarkedExcellentEvent) {
            $this->markedExcellent($event->target);
        } elseif ($event instanceof ThreadWasPinnedEvent){
            $this->threadPinned($event->target);
        } elseif ($event instanceof ReplyWasPinnedEvent){
            $this->replyPinned($event->target);
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
        $type = null;
        if ($target instanceof Thread) {
            $type = 'thread_like';
        } elseif ($target instanceof Reply) {
            $type = 'reply_like';
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

    protected function replyPinned($target)
    {
        app('notifier')->notify('reply_pin', Auth::user(), $target->user, $target);
    }

}
