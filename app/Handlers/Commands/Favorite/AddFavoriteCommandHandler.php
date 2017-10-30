<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Commands\Favorite;

use Auth;
use Hifone\Commands\Favorite\AddFavoriteCommand;
use Hifone\Events\Favorite\FavoriteThreadWasAddedEvent;
use Hifone\Events\Favorite\FavoriteThreadWasRemovedEvent;
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Events\Favorite\FavoriteWasRemovedEvent;
use Hifone\Models\Favorite;
use Hifone\Services\Dates\DateFactory;

class AddFavoriteCommandHandler
{
    /**
     * The date factory instance.
     *
     * @var \Hifone\Services\Dates\DateFactory
     */
    protected $dates;

    /**
     * Create a new report issue command handler instance.
     *
     * @param \Hifone\Services\Dates\DateFactory $dates
     */
    public function __construct(DateFactory $dates)
    {
        $this->dates = $dates;
    }

    /**
     * Handle the report avorite command.
     *
     * @param \Hifone\Commands\Thread\AddThreadCommand $command
     *
     * @return \Hifone\Models\Thread
     */
    public function handle(AddFavoriteCommand $command)
    {
        $this->favoriteAction($command->target);
    }

    protected function favoriteAction($thread)
    {
        if (Favorite::isUserFavoritedThread(Auth::user(), $thread)) {
            Auth::user()->favoriteThreads()->detach($thread->id);
            if ($thread->favorite_count > 0){
                $thread->decrement('favorite_count', 1);
            }

            event(new FavoriteWasRemovedEvent($thread->user));//帖子被取消收藏，被动事件
            event(new FavoriteThreadWasRemovedEvent(Auth::user()));//取消对帖子的收藏，主动事件
        } else {
            Auth::user()->favoriteThreads()->attach($thread->id);
            $thread->increment('favorite_count', 1);

            event(new FavoriteWasAddedEvent($thread));//帖子被收藏，被动事件
            event(new FavoriteThreadWasAddedEvent(Auth::user()));//收藏帖子，主动事件
        }
    }
}
