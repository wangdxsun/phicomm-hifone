<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers;

use Hifone\Http\Bll\FavoriteBll;
use Hifone\Models\Thread;

class FavoriteController extends Controller
{
    public function createOrDeleteFavorite(Thread $thread, FavoriteBll $favoriteBll)
    {
        return $favoriteBll->createOrDelete($thread);
    }
}
