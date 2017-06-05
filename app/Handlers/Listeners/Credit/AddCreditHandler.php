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
use Hifone\Events\Follow\FollowWasRemovedEvent;
use Hifone\Events\Image\ImageWasUploadedEvent;
use Hifone\Events\Like\LikeWasRemovedEvent;
use Hifone\Events\Reply\ReplyWasAddedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Thread\ThreadWasAddedEvent;
use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Events\User\UserWasLoggedinEvent;
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Events\Favorite\FavoriteWasRemovedEvent;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Pin\SinkWasAddedEvent;
use Hifone\Events\Follow\FollowWasAddedEvent;
use Hifone\Events\Follow\FollowedWasAddedEvent;
use Hifone\Events\Follow\FollowedWasRemovedEvent;
use Hifone\Events\Excellent\ExcellentWasAddedEvent;
use Hifone\Events\Like\LikeWasAddedEvent;
use Hifone\Events\Like\LikedWasAddedEvent;
use Hifone\Events\Like\LikedWasRemovedEvent;
use Hifone\Events\Image\AvatarWasUploadedEvent;
use Hifone\Models\Thread;
use Hifone\Models\User;

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
            $user = $event->threadUser;
            if ($event->threadUser->id == $event->replyUser->id) {
                return false;
            }
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
            $action = 'favorite';
            $user = $event->thread->user;
            if (Auth::id() == $user->id) {//收藏自己的帖子
                return false;
            }
        } elseif ($event instanceof FavoriteWasRemovedEvent) {
            $action = 'favorite_removed';
            $user = $event->user;
        } elseif ($event instanceof PinWasAddedEvent) {
            if($event->action == 'Thread'){
                $action = 'thread_pin';
            }elseif($event->action == 'Reply'){
                $action = 'replied_pin';
            }
            $user = $event->user;
        }elseif ($event instanceof SinkWasAddedEvent) {
            $action = 'thread_down';
            $user = $event->user;
        } elseif ($event instanceof FollowWasAddedEvent) {
            if ($event->target instanceof Thread) {
                $action = 'follow_thread';
            } else {
                $action = 'follow_user';
            }
            $user = Auth::user();
        } elseif ($event instanceof FollowWasRemovedEvent) {
            if ($event->target instanceof Thread) {
                $action = 'follow_thread_removed';
            } else {
                $action = 'follow_user_removed';
            }
            $user = Auth::user();
        } elseif ($event instanceof FollowedWasAddedEvent) {
            if ($event->target instanceof Thread) {
                $action = 'followed_thread';
                $user = $event->target->user;
            } else {
                $action = 'followed_user';
                $user = $event->target;
            }

        } elseif ($event instanceof FollowedWasRemovedEvent) {
            if ($event->target instanceof Thread) {
                $action = 'followed_thread_removed';
                $user = $event->target->user;
            } else {
                $action = 'followed_user_removed';
                $user = $event->target;
            }
        } elseif ($event instanceof ExcellentWasAddedEvent) {
            $action = 'thread_excellent';
            $user = $event->target;
        } elseif ($event instanceof LikeWasAddedEvent) {
            $action = 'like';
            $user = $event->target;
            if (Auth::id() == $user->id) {
                return false;
            }
        } elseif ($event instanceof LikeWasRemovedEvent) {
            $action = 'like_removed';
            $user = $event->target;
            if (Auth::id() == $user->id) {
                return false;
            }
        } elseif ($event instanceof LikedWasAddedEvent) {
            $action = 'liked';
            $user = $event->target;
            if (Auth::id() == $user->id) {
                return false;
            }
        } elseif ($event instanceof LikedWasRemovedEvent) {
            $action = 'liked_removed';
            $user = $event->target;
            if (Auth::id() == $user->id) {
                return false;
            }
        } elseif ($event instanceof AvatarWasUploadedEvent) {
            $action = 'upload_avatar';
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
    }
}
