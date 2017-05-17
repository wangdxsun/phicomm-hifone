<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/9
 * Time: 8:47
 */

namespace Hifone\Http\Controllers\Api;

use Auth;
use Hifone\Http\Bll\FollowBll;
use Hifone\Http\Bll\UserBll;
use Hifone\Models\User;

class UserController extends ApiController
{
    public function me()
    {
        $user = Auth::user();
        $user['role'] = $user->role;

        return $user;
    }

    public function show(User $user, UserBll $userBll)
    {
        $user['role'] = $user->role;

        return $user;
    }

    public function follows(User $user, FollowBll $followBll)
    {
        $follows = $followBll->follows($user);
        foreach ($follows as &$follow) {
            $follow['followed'] = $follow['follower']->hasFollowUser($user);
        }

        return $follows;
    }

    public function followers(User $user, FollowBll $followBll)
    {
        $followers = $followBll->followers($user);
        foreach ($followers as &$follower) {
            $follower['followed'] = $user->hasFollowUser($follower['follower']);
        }

        return $followers;
    }

    public function threads(User $user, UserBll $userBll)
    {
        $threads = $userBll->getThreads($user);

        return $threads;
    }

    public function credit(UserBll $userBll)
    {
        $credits = $userBll->getCredits();

        return $credits;
    }

    public function replies(UserBll $userBll)
    {
        $replies = $userBll->getReplies();

        return $replies;
    }
}