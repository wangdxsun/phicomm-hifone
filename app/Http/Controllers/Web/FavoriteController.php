<?php

namespace Hifone\Http\Controller\Web;

use Hifone\Http\Bll\FavoriteBll;
use Hifone\Models\Thread;

class FavoriteController extends WebController
{
    public function threadFavorite(Thread $thread, FavoriteBll $favoriteBll)
    {
        return $favoriteBll->favoriteThread($thread);
    }
}