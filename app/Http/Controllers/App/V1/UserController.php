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
use Hifone\Http\Bll\BannerBll;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\FollowBll;
use Hifone\Http\Bll\PhicommBll;
use Hifone\Http\Bll\UserBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Node;
use Hifone\Models\Question;
use Hifone\Models\Section;
use Hifone\Models\User;
use Hifone\Services\Filter\WordsFilter;
use Auth;

class UserController extends AppController
{
    //获取当前用户信息
    public function me(PhicommBll $phicommBll, UserBll $userBll)
    {
        if (empty(Auth::phicommId())) {
            throw new HifoneException('缺少token');
        }
        $user = User::findUserByPhicommId(Auth::phicommId());
        if (!$user) {
            throw new HifoneException('请先关联社区账号', 500);
        }
        if (array_get($user, 'phicomm_id')) {
            $cloudUser = $phicommBll->userInfo();
            if (array_get($cloudUser, 'img') && $user->avatar_url != $cloudUser['img'] && $cloudUser['img'] != 'Uploads/default/default.jpg') {
                $user->avatar_url = $cloudUser['img'];
                $user->save();
            }
            if (array_get($cloudUser, 'phonenumber') && $cloudUser['phonenumber'] !== $user->phone) {
                $user->phone = $cloudUser['phonenumber'];
                $user->save();
            }
        }
        $user['isAdmin'] = ($user->role =='管理员' || $user->role =='创始人');
        $user['follow_new_answer_count'] = $userBll->getFollowNewAnswerCount($user);

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
        $user = $phicommBll->bind($wordsFilter);
        $user = $user->toArray();
        unset($user['roles']);
        return $user;
    }

    public function show(User $user)
    {
        $user['followed'] = User::hasFollowUser($user);
        return $user;
    }

    //我关注的人
    public function follows(User $user, FollowBll $followBll)
    {
        $follows = $followBll->follows($user);
        foreach ($follows as $follow) {
            $follow['followed'] = User::hasFollowUser($follow['follower']);
        }

        return $follows;
    }

    //我的粉丝
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

    //我关注的问题
    public function followQuestions(User $user, FollowBll $followBll)
    {
        $questions = $followBll->followQuestions($user);

        return $questions;
    }

    //我发出的帖子
    public function threads(User $user, UserBll $userBll)
    {
        $threads = $userBll->getThreads($user);

        return $threads;
    }

    //我发出的评论
    public function replies(User $user, UserBll $userBll)
    {
        $replies = $userBll->getReplies($user);

        return $replies;
    }

    //我发出的提问
    public function questions(User $user, UserBll $userBll)
    {
        $replies = $userBll->getQuestions($user);

        return $replies;
    }

    //我发出的回答
    public function answers(User $user, UserBll $userBll)
    {
        $replies = $userBll->getAnswers($user);

        return $replies;
    }

    //邀请专家用户列表
    public function expertUsers(User $user, Question $question, UserBll $userBll)
    {
        return $userBll->getExpertUsers($user, $question);
    }

    //邀请关注用户列表
    public function followUsers(User $user, Question $question, UserBll $userBll)
    {
        return $userBll->getFollowUsers($user, $question);
    }

    public function credit(UserBll $userBll, CommonBll $commonBll)
    {
        $commonBll->loginApp();
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

    public function threadFeedbacks(UserBll $userBll)
    {
        return $userBll->getThreadFeedbacks();
    }

    public function feedbacks(UserBll $userBll)
    {
        return $userBll->getFeedbacks();
    }

    public function replyFeedbacks(UserBll $userBll)
    {
        return $userBll->getReplyFeedbacks();
    }

    //用户关注版块列表
    public function sections(User $user)
    {
        $sections = Section::orderBy('order')->whereHas('nodes', function ($query) use ($user) {
            $query->has('subNodes')->whereIn('id', $user->followedNodes->pluck('id'));
        })->with(['nodes' => function ($query) use ($user) {
            $query->whereIn('id', $user->followedNodes->pluck('id'));
        }])->get();

        return $sections;

    }

}