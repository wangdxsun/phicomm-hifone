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
use Hifone\Models\Like;
use Hifone\Models\User;

class AddLikeCommandHandler
{
    public function handle(AddLikeCommand $command)
    {
        $this->likeAction($command->target);
    }

    protected function likeAction($target)
    {
        \DB::transaction(function () use ($target) {
            $user = User::lockForUpdate()->find($target->user_id);
            if ($target->likes()->forUser(Auth::id())->WithUp()->count()) {
                //取消点赞
                $target->likes()->forUser(Auth::id())->WithUp()->delete();
                $target->decrement('like_count', 1);

                event(new LikeWasRemovedEvent(Auth::user(), $target));
                event(new LikedWasRemovedEvent($user));
            } else {
                //点赞
                $target->likes()->create(['user_id' => Auth::id(), 'rating' => Like::LIKE]);
                $target->increment('like_count', 1);

                event(new LikeWasAddedEvent(Auth::user(), $target));  //用户主动点赞like
                event(new LikedWasAddedEvent($user, $target));//被赞liked
            }
        });
    }

}
