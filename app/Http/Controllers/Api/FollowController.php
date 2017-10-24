<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/4/25
 * Time: 15:27
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\FollowBll;
use Hifone\Models\Thread;
use Hifone\Models\User;

class FollowController extends ApiController
{
    public function user(User $user, FollowBll $followBll)
    {
        return $followBll->followUser($user);
    }

    public function thread(Thread $thread, FollowBll $followBll)
    {
        return $followBll->followThread($thread);
    }
}