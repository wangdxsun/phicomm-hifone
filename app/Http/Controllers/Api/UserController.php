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
use Illuminate\Http\JsonResponse;

class UserController extends ApiController
{
    public function me()
    {
        $user = Auth::user();
        if (Auth::bind() == false) {
            return new JsonResponse('unbind.', 400);
        }
        if (! is_null($user)) {
            $user['role'] = $user->role;
            return $user;
        } else {
            return 'PhicommNoLogin';
        }
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
            $follow['role'] = $follow->follower->role;
        }

        return $follows;
    }

    public function followers(User $user, FollowBll $followBll)
    {
        $followers = $followBll->followers($user);
        foreach ($followers as &$follower) {
            $follower['followed'] = User::hasFollowUser($follower['user']);
            $follower['role'] = $follower->user->role;
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