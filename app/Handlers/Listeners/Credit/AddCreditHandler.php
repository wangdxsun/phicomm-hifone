<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Listeners\Credit;

use Auth;
use Hifone\Commands\Credit\AddCreditCommand;
use Hifone\Events\Credit\CreditWasAddedEvent;
use Hifone\Events\EventInterface;
use Hifone\Events\Image\ImageWasUploadedEvent;
use Hifone\Events\Reply\ReplyWasAddedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Thread\ThreadWasAddedEvent;
use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Events\User\UserWasLoggedinEvent;
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Events\Favorite\FavoriteWasRemovedEvent;
use Hifone\Events\Thread\ThreadWasPinnedEvent;
use Hifone\Events\Follow\FollowWasAddedEvent;
use Hifone\Events\Follow\FollowedWasAddedEvent;
use Hifone\Events\Follow\FollowedWasRemovedEvent;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Like\LikeWasAddedEvent;
use Hifone\Events\Like\LikedWasAddedEvent;
use Hifone\Events\Like\LikedWasRemovedEvent;

class AddCreditHandler
{
    public function handle(EventInterface $event)
    {
        $action = '';
        if ($event instanceof ThreadWasAddedEvent) {
            $action = 'thread_new';
            $user = $event->thread->user;
        } elseif ($event instanceof ReplyWasAddedEvent) {
            $action = 'reply_new';
            $user = $event->reply->user;
        } elseif ($event instanceof RepliedWasAddedEvent) {
            $action = 'replied';
            $user = $event->user;
        } elseif ($event instanceof ImageWasUploadedEvent) {
            $action = 'photo_upload';
            $user = Auth::user();
        } elseif ($event instanceof UserWasAddedEvent) {
            $action = 'register';
            $user = $event->user;
        } elseif ($event instanceof UserWasLoggedinEvent) {
            $action = 'login';
            $user = $event->user;
        } elseif ($event instanceof FavoriteWasAddedEvent) {
            $action = 'favorited';
            $user = $event->user;
        } elseif ($event instanceof FavoriteWasRemovedEvent) {
            $action = 'favorited_removed';
            $user = $event->user;
        } elseif ($event instanceof ThreadWasPinnedEvent) {
            $action = 'thread_pin';
            $user = $event->user;
        }elseif ($event instanceof FollowWasAddedEvent) {
            $action = 'follow';
            $user = $event->target;
        }elseif ($event instanceof FollowedWasAddedEvent) {
            $action = 'followed';
            $user = $event->target;
        }elseif ($event instanceof FollowedWasRemovedEvent) {
            $action = 'followed_removed';
            $user = $event->target;
        }elseif ($event instanceof ExcellentWasAddedEvent) {
            $action = 'thread_excellent';
            $user = $event->target;
        }
        elseif ($event instanceof LikeWasAddedEvent) {
            $action = 'like';
            $user = $event->target;
        }elseif ($event instanceof LikedWasAddedEvent) {
            $action = 'liked';
            $user = $event->target;
        }elseif ($event instanceof LikedWasRemovedEvent) {
            $action = 'liked_removed';
            $user = $event->target;
        }

        $this->apply($event, $action, $user);
    }

    protected function apply($event, $action, $user)
    {
        if (!$action) {
            return;
        }

        $credit = dispatch(new AddCreditCommand($action, $user));

        if (!$credit) {
            return;
        }

        // event trigger
        event(new CreditWasAddedEvent($credit, $event));
    }
}
