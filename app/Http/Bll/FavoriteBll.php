<?php

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Commands\Favorite\AddFavoriteCommand;
use Hifone\Models\Thread;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoriteBll extends BaseBll
{
    public function favoriteThread(Thread $thread)
    {
        if ($thread->status <> Thread::VISIBLE) {
            throw new NotFoundHttpException('该帖子已被删除');
        }
        dispatch(new AddFavoriteCommand($thread));

        return ['favorite' => Auth::user()->hasFavoriteThread($thread)];
    }
}