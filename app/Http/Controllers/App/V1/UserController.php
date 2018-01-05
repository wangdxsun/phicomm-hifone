<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/1
 * Time: 15:08
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Commands\Image\UploadImageCommand;
use Hifone\Events\Image\AvatarWasUploadedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\FollowBll;
use Hifone\Http\Bll\PhicommBll;
use Hifone\Http\Bll\UserBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\User;
use Hifone\Services\Filter\WordsFilter;
use Auth;

class UserController extends AppController
{
    /**
     * 获取当前用户信息
     * @return mixed
     * @throws \Exception
     */
    public function me()
    {
        if (empty(Auth::phicommId())) {
            throw new HifoneException('缺少token');
        }
        $user = User::findUserByPhicommId(Auth::phicommId());
        if (!$user) {
            throw new HifoneException('请先关联社区账号');
        }
        return $user;
    }

    //绑定社区用户
    public function bind(PhicommBll $phicommBll, WordsFilter $wordsFilter)
    {
        $this->validate(request(), [
            'username' => 'required|max:15|regex:/\A[\x{4e00}-\x{9fa5}A-Za-z0-9\-\_\.]+\z/u',
        ], [
            'username.regex' => '昵称含有非法字符'
        ]);
        $phicommBll->bind($wordsFilter);

        return success('创建成功');
    }

    public function show(User $user)
    {
        $user['followed'] = User::hasFollowUser($user);
        return $user;
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
        $commonBll->login();
        $credits = $userBll->getCredits();

        return $credits;
    }

    //上传头像
    public function upload(PhicommBll $phicommBll)
    {
        if (!request()->hasFile('image')) {
            throw new HifoneException('没有上传图片');
        }
        $avatar = dispatch(new UploadImageCommand(request()->file('image')));
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
        return $users;
    }

    public function favorites(User $user, UserBll $userBll)
    {
        $threads = $userBll->getFavorites($user);
        return $threads;
    }

    public function feedbacks(UserBll $userBll)
    {
        return $userBll->getFeedbacks();
    }
}