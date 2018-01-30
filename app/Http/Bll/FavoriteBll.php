<?php

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Commands\Favorite\AddFavoriteCommand;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Thread;

class FavoriteBll extends BaseBll
{
    public function favoriteThread(Thread $thread)
    {
        if ($thread->status <> Thread::VISIBLE) {
            throw new HifoneException('该帖子已被删除', 410);
        }
        dispatch(new AddFavoriteCommand($thread));

        return ['favorite' => Auth::user()->hasFavoriteThread($thread)];
    }
}