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
use Hifone\Events\Favorite\FavoriteWasAddedEvent;
use Hifone\Events\Favorite\FavoriteWasRemovedEvent;
use Hifone\Models\Favorite;
use Hifone\Models\Thread;
use Hifone\Models\User;
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

            event(new FavoriteWasRemovedEvent($thread->user));
        } else {
            Auth::user()->favoriteThreads()->attach($thread->id);

            event(new FavoriteWasAddedEvent($thread));
        }
    }
}
