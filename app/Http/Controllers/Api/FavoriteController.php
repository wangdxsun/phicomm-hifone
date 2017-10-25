<?php

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\FavoriteBll;
use Hifone\Models\Thread;

class FavoriteController extends ApiController
{
    public function createOrDeleteFavorite(Thread $thread, FavoriteBll $favoriteBll)
    {
        return $favoriteBll->favoriteThread($thread);
    }
}