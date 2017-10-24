<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/6
 * Time: 15:03
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Like\AddLikeCommand;
use Auth;

class LikeBll extends BaseBll
{
    public function likeThread($thread)
    {
        dispatch(new AddLikeCommand($thread));

        return ['liked' => Auth::user()->hasLikeThread($thread)];
    }

    public function likeReply($reply)
    {
        dispatch(new AddLikeCommand($reply));

        return ['liked' => Auth::user()->hasLikeReply($reply)];
    }
}