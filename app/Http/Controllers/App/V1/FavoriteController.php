<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/20
 * Time: 16:00
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\FavoriteBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Thread;

class FavoriteController extends AppController
{
    public function createOrDeleteFavorite(Thread $thread, FavoriteBll $favoriteBll)
    {
        return $favoriteBll->favoriteThread($thread);
    }
}