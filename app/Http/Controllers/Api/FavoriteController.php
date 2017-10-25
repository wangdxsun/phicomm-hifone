<?php
namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\FavoriteBll;
use Hifone\Models\Thread;
use Response;

class FavoriteController extends ApiController
{
    public function thread(Thread $thread, FavoriteBll $favoriteBll)
    {
        $favoriteBll->favorite($thread);
        return Response::json('success');
    }
}