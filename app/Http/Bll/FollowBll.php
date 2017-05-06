<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/5
 * Time: 17:38
 */

namespace Hifone\Http\Bll;

use Auth;
use Exception;
use Hifone\Commands\Follow\AddFollowCommand;

class FollowBll extends BaseBll
{
    public function followUser($user)
    {
        if ($user->id == Auth::id()) {
            throw new Exception('不能关注自己');
        }
        return dispatch(new AddFollowCommand($user));
    }

    public function followThread($thread)
    {
        if ($thread->user->id == Auth::id()) {
            throw new Exception('自己的帖子无需关注');
        }
        return dispatch(new AddFollowCommand($thread));
    }
}