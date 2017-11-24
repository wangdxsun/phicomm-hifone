<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Commands\Like;

use Auth;
use Hifone\Commands\Like\AddLikeCommand;
use Hifone\Events\Like\LikeWasAddedEvent;
use Hifone\Events\Like\LikedWasAddedEvent;
use Hifone\Events\Like\LikedWasRemovedEvent;
use Hifone\Events\Like\LikeWasRemovedEvent;
use Hifone\Events\Thread\ThreadWasLikedEvent;
use Hifone\Models\Like;
use Hifone\Models\User;
use Hifone\Services\Dates\DateFactory;

class AddLikeCommandHandler
{
    public function handle(AddLikeCommand $command)
    {
        if ($command->action == 'like') {
            $this->likeAction($command->target);
        } else {
            $this->unlikeAction($command->target);
        }
    }

    protected function likeAction($target)
    {
        $user = User::find($target->user_id);
        if ($target->likes()->forUser(Auth::id())->WithUp()->sharedLock()->count()) {
            \DB::transaction(function () use ($target, $user) {
                $target->likes()->forUser(Auth::id())->WithUp()->delete();
                $target->decrement('like_count', 1);

                event(new LikeWasRemovedEvent(Auth::user()));
                event(new LikedWasRemovedEvent($user));
            });
        } elseif ($target->likes()->forUser(Auth::id())->WithDown()->sharedLock()->count()) {
            // user already clicked unlike once
            $target->likes()->forUser(Auth::id())->WithDown()->delete();
            $target->likes()->create(['user_id' => Auth::id(), 'rating' => Like::LIKE]);
            $target->increment('like_count', 2);

            event(new ThreadWasLikedEvent($target));//点赞帖子或回复
            event(new LikeWasAddedEvent(Auth::user()));
            event(new LikedWasAddedEvent($user));
        } else {
            \DB::transaction(function () use ($target, $user) {
                $target->likes()->create(['user_id' => Auth::id(), 'rating' => Like::LIKE]);
                $target->increment('like_count', 1);

                event(new ThreadWasLikedEvent($target));
                event(new LikeWasAddedEvent(Auth::user()));
                event(new LikedWasAddedEvent($user));
            });
        }
    }

    protected function unlikeAction($target)
    {
        $user = User::find($target->user_id);
        if ($target->likes()->forUser(Auth::id())->WithDown()->count()) {
            // click second time for remove unlike
            $target->likes()->forUser(Auth::id())->WithDown()->delete();
            $target->increment('like_count', 1);

            event(new LikedWasAddedEvent($user));
        } elseif ($target->likes()->forUser(Auth::id())->WithUp()->count()) {
            // user already clicked like once
            $target->likes()->forUser(Auth::id())->WithUp()->delete();
            $target->likes()->create(['user_id' => Auth::id(), 'rating' => Like::UNLIKE]);
            $target->decrement('like_count', 2);

            event(new LikeWasAddedEvent(Auth::user()));
            event(new LikedWasAddedEvent($user));
        } else {
            // click first time
            $target->likes()->create(['user_id' => Auth::id(), 'rating' => Like::UNLIKE]);
            $target->decrement('like_count', 1);

            event(new ThreadWasLikedEvent($target));
            event(new LikedWasRemovedEvent($user));
        }
    }
}
