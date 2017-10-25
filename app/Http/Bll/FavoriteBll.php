<?php
namespace Hifone\Http\Bll;

use Hifone\Commands\Favorite\AddFavoriteCommand;
use Hifone\Models\Thread;

class FavoriteBll extends BaseBll
{
    public function favorite(Thread $thread)
    {
        dispatch(new AddFavoriteCommand($thread));
    }
}