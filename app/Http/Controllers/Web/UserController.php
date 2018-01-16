<?php

namespace Hifone\Http\Controllers\Web;

use Auth;
use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Events\Image\AvatarWasUploadedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\FollowBll;
use Hifone\Http\Bll\PhicommBll;
use Hifone\Http\Bll\UserBll;
use Hifone\Models\User;
use Illuminate\Http\JsonResponse;
use Str;

class UserController extends WebController
{
    public function me(PhicommBll $phicommBll, UserBll $userBll)
    {
        $user = Auth::user();
        if (Auth::bind() == false) {
            return new JsonResponse('unbind.', 400);
        }
        if (! is_null($user)) {
            $cloudUser = $phicommBll->userInfo();
            if ($cloudUser['img'] && $user->avatar_url != $cloudUser['img'] && $cloudUser['img'] != 'Uploads/default/default.jpg') {
                $user->avatar_url = $cloudUser['img'];
                $user->save();
                $user->updateIndex();
            }
            if ($cloudUser['phonenumber'] !== $user->phone) {
                $user->phone = $cloudUser['phonenumber'];
                $user->save();
            }
            $user['isAdmin'] = $user->role =='管理员' || $user->role =='创始人';
            $userBll->webUpdateActiveTime();
            return $user;
        } else {
            return 'PhicommNoLogin';
        }
    }

    public function show(User $user, UserBll $userBll)
    //用户个人中心
    {
        $user = User::withCount(['favorites' => function ($query) {
            $query->has('thread');
        }])->find($user->id);
        $user['followed'] = User::hasFollowUser($user);
        $userBll->webUpdateActiveTime();

        return $user;
    }

    public function showByUsername(User $user, UserBll $userBll)
    {
        return $this->show($user, $userBll);
    }

    public function follows(User $user, FollowBll $followBll)
    {
        $follows = $followBll->follows($user);
        foreach ($follows as $follow) {
            $follow['followed'] = User::hasFollowUser($follow['follower']);
        }

        return $follows;
    }

    public function followers(User $user, FollowBll $followBll)
    {
        $followers = $followBll->followers($user);
        foreach ($followers as $follower) {
            if (isset($follower['user'])) {
                $follower['followed'] = User::hasFollowUser($follower['user']);
            }
        }

        return $followers;
    }

    public function threads(User $user, UserBll $userBll)
    {
        $threads = $userBll->getThreads($user);

        return $threads;
    }

    public function replies(User $user, UserBll $userBll)
    {
        $replies = $userBll->getReplies($user);

        return $replies;
    }

    public function credit(UserBll $userBll, CommonBll $commonBll)
    {
        $commonBll->loginWeb();
        $credits = $userBll->getCredits();

        return $credits;
    }

    //上传头像
    public function upload(CommonBll $commonBll, PhicommBll $phicommBll)
    {
        if (!request()->has('image')) {
            throw new HifoneException('没有上传图片');
        }
        $avatar = dispatch(new UploadBase64ImageCommand(request('image')));
        Auth::user()->update(['avatar_url' => $avatar['filename']]);
        Auth::user()->updateIndex();
        $phicommBll->upload($avatar['localFile']);
        event(new AvatarWasUploadedEvent(Auth::user()));
        unset($avatar['localFile']);

        return $avatar;
    }

    public function search($keyword, UserBll $userBll)
    {
        $users = $userBll->search($keyword);
        $userBll->webUpdateActiveTime();
        return $users;
    }

    public function favorites(User $user, UserBll $userBll)
    {
        $threads = $userBll->getFavorites($user);

        return $threads;
    }

    public function excellentUsers(UserBll $userBll)
    {
        $users = $userBll->getExcellentUsers();
        return $users;
    }
}