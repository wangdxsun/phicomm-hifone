<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Commands\Follow;

use Auth;
use Hifone\Commands\Follow\AddFollowCommand;
use Hifone\Events\Follow\FollowWasAddedEvent;
use Hifone\Events\Follow\FollowedWasAddedEvent;
use Hifone\Events\Follow\FollowedWasRemovedEvent;
use Hifone\Events\Follow\FollowWasRemovedEvent;
use Hifone\Models\User;
use DB;

class AddFollowCommandHandler
{
    /**
     * Handle the report avorite command.
     *
     * @param \Hifone\Commands\Thread\AddThreadCommand $command
     *
     * @return \Hifone\Models\Thread
     */
    public function handle(AddFollowCommand $command)
    {
        $this->followAction($command->target);
    }

    protected function followAction($target)
    {
        DB::transaction(function () use ($target) {
            if ($target->followers()->forUser(Auth::id())->count()) {
                $target->followers()->forUser(Auth::id())->delete();
                $target->decrement('follower_count', 1);
                if ($target instanceOf User) {
                    Auth::user()->decrement('follow_count', 1);
                    Auth::user()->updateIndex();
                }

                event(new FollowWasRemovedEvent($target));
                event(new FollowedWasRemovedEvent($target));
            } else {
                $target->followers()->create(['user_id' => Auth::id()]);
                $target->increment('follower_count', 1);
                if ($target instanceOf User) {
                    Auth::user()->increment('follow_count', 1);
                    Auth::user()->updateIndex();
                }
                event(new FollowWasAddedEvent($target));
                event(new FollowedWasAddedEvent($target));
            }
        });
    }
}
