<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/20
 * Time: 16:16
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\LikeBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Response;

class LikeController extends AppController
{
    public function thread(Thread $thread, LikeBll $likeBll)
    {
        return $likeBll->likeThread($thread);
    }

    public function reply(Reply $reply, LikeBll $likeBll)
    {
        return $likeBll->likeReply($reply);
    }
}