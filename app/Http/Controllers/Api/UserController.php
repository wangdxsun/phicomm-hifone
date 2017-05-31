<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/9
 * Time: 8:47
 */

namespace Hifone\Http\Controllers\Api;

use Auth;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\FollowBll;
use Hifone\Http\Bll\UserBll;
use Hifone\Models\User;

class UserController extends ApiController
{
    public function me()
    {
        $user = Auth::user();
        if (Auth::bind() == false) {
            throw new \Exception('unbind', 400);
        }
        $user['role'] = $user->role;

        return $user;
    }

    public function show(User $user)
    {
        $user['role'] = $user->role;
        $user['followed'] = User::hasFollowUser($user);

        return $user;
    }

    public function showByUsername(User $user)
    {
        return $this->show($user);
    }

    public function follows(User $user, FollowBll $followBll)
    {
        $follows = $followBll->follows($user);
        foreach ($follows as &$follow) {
            $follow['followed'] = User::hasFollowUser($follow['follower']);
        }

        return $follows;
    }

    public function followers(User $user, FollowBll $followBll)
    {
        $followers = $followBll->followers($user);
        foreach ($followers as &$follower) {
            $follower['followed'] = User::hasFollowUser($follower['user']);
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

    /**
     * 上传头像
     */
    public function upload(CommonBll $commonBll)
    {
        $avatar = $commonBll->upload();
        Auth::user()->update(['avatar_url' => $avatar['filename']]);

        return $avatar;
    }
}