<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/6
 * Time: 15:04
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\LikeBll;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Response;

class LikeController extends ApiController
{
    public function thread(Thread $thread, LikeBll $likeBll)
    {
        $likeBll->likeThread($thread);

        return Response::json('success');
    }

    public function reply(Reply $reply, LikeBll $likeBll)
    {
        $likeBll->likeReply($reply);

        return Response::json('success');
    }
}