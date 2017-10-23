<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/23
 * Time: 14:17
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\FavoriteBll;
use Hifone\Models\Thread;

class FavoriteController extends ApiController
{
    public function createOrDeleteFavorite(Thread $thread, FavoriteBll $favoriteBll)
    {
        return $favoriteBll->createOrDelete($thread);
    }
}