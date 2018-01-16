<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/9
 * Time: 8:47
 */

namespace Hifone\Http\Bll;

use Auth;
use Config;
use Hifone\Events\User\UserWasActiveEvent;
use Hifone\Models\User;

class UserBll extends BaseBll
{
    public function getCredits()
    {
        $credits = Auth::user()->credits()->where('body', '<>', '0')->with(['rule'])->recent()->paginate();

        return $credits;
    }

    public function getThreads(User $user)
    {
        //自己查看帖子，接口信息包括所有贴子;管理员和其他用户，只包括审核通过和审核通过被删除的贴子
        if (Auth::check() && $user->id == Auth::id()) {
            $threads = $user->threads()->with(['user', 'node'])->recent()->paginate();
        } else {
            $threads = $user->threads()->visibleAndDeleted()->with(['user', 'node'])->recent()->paginate();
        }
        return $threads;
    }

    public function getReplies(User $user)
    {
        $replies = $user->replies()->visibleAndDeleted()->whereHas('thread', function ($query) {
            $query->visibleAndDeleted();
        })->with(['user', 'thread', 'reply.user'])->recent()->paginate();

        return $replies;
    }

    //全局搜索用户
    public function search($keyword)
    {
        $users = User::searchUser($keyword)->paginate(15);
        foreach ($users as $user) {
            $user['followed'] = User::hasFollowUser($user);
        }
        return $users;
    }

    //个人收藏帖子列表
    public function getFavorites(User $user)
    {
        $threads = $user->favorites()->with(['thread.user', 'thread.node'])->has('thread')->paginate();
        return $threads;
    }

    //查看自己反馈历史记录（含所有状态）
    public function getFeedbacks()
    {
        if (Auth::check()){
            $feedbacks = Auth::user()->threads()->feedback()->recent()->paginate();
        } else {
            $feedbacks = [];
        }
        return $feedbacks;
    }

}