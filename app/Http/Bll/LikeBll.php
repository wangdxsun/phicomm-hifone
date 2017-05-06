<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/6
 * Time: 15:03
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Like\AddLikeCommand;

class LikeBll extends BaseBll
{
    public function likeThread($thread)
    {
        dispatch(new AddLikeCommand($thread));
    }

    public function likeReply($reply)
    {
        dispatch(new AddLikeCommand($reply));
    }
}