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
        //管理员查看帖子，接口信息只包括审核通过和审核通过被删除的贴子
        if (Auth::check() && $user->id == Auth::id()) {
            $threads = $user->threads()->with(['user', 'node'])->recent()->paginate();
        } else {
            $threads = $user->threads()->visibleAndDeleted()->with(['user', 'node'])->recent()->paginate();
        }
        return $threads;
    }

    public function getReplies(User $user)
    {
        $replies = $user->replies()->visibleAndDeleted()->with(['thread'])->recent()->paginate();
        foreach ($replies as $key => $reply) {
            if (!$reply->thread->visible) {
                unset($replies[$key]);
            }
        }
        return $replies;
    }

    public function search()
    {
        return [];
    }

    //个人收藏帖子列表
    public function getFavorites(User $user)
    {
        $threads = $user->favorites()->with(['thread.user', 'thread.node'])->has('thread')->paginate();
        return $threads;
    }

    //查看自己反馈历史记录
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