<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\FollowBll;
use Hifone\Models\Thread;
use Hifone\Models\User;

class FollowController extends WebController
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