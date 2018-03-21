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
        if (Auth::bind() == false) {//如果云账号已登录，但是未关联社区账号，$user也为null，所以这个逻辑要放在前面
            return 'Unbind';
        }elseif (is_null($user)) {
            return 'PhicommNoLogin';
        } elseif (array_get($user, 'phicomm_id')) {//如果有云账号id，要同步云账号信息
            $cloudUser = $phicommBll->userInfo();
            if (array_get($cloudUser, 'img') && $user->avatar_url != $cloudUser['img'] && $cloudUser['img'] != 'Uploads/default/default.jpg') {
                $user->avatar_url = $cloudUser['img'];
                $user->save();
                $user->updateIndex();
            }
            if (array_get($cloudUser, 'phonenumber') && $cloudUser['phonenumber'] !== $user->phone) {
                $user->phone = $cloudUser['phonenumber'];
                $user->save();
            }
        }
        $user['isAdmin'] = ($user->role =='管理员' || $user->role =='创始人');
        $user['draft_count'] = $user->threads()->draft()->count();

        return $user;
    }

    public function show(User $user, UserBll $userBll)
    //用户个人中心
    {
        $user = User::withCount(['favorites' => function ($query) {
            $query->has('thread');
        }])->find($user->id);
        $user['followed'] = User::hasFollowUser($user);

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

    public function drafts(User $user, UserBll $userBll)
    {
        $drafts = $userBll->getDrafts($user);

        return $drafts;
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
        if (Auth::phicommId()) {
            $phicommBll->upload($avatar['localFile']);
        }
        event(new AvatarWasUploadedEvent(Auth::user()));
        unset($avatar['localFile']);

        return $avatar;
    }

    public function search($keyword, UserBll $userBll)
    {
        $users = $userBll->search($keyword);
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