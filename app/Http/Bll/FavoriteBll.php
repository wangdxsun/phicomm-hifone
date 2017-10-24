<?php

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Commands\Favorite\AddFavoriteCommand;
use Hifone\Models\Thread;

class FavoriteBll extends BaseBll
{

    public function favoriteThread(Thread $thread)
    {
        dispatch(new AddFavoriteCommand($thread));

        return ['favorite' => Auth::check() ? Auth::user()->hasFavoriteThread($thread) : false];
    }
}