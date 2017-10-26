<?php

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\FavoriteBll;
use Hifone\Models\Thread;

class FavoriteController extends ApiController
{
    public function threadFavorite(Thread $thread, FavoriteBll $favoriteBll)
    {
        return $favoriteBll->favoriteThread($thread);
    }
}