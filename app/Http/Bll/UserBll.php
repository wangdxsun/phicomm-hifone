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
        //自己或管理员查看帖子，接口信息包括所有贴子
        if (Auth::check() && ($user->id == Auth::id() || Auth::user()->can('view_thread'))) {
            $threads = $user->threads()->with(['user', 'node'])->recent()->paginate();
        } else {
            $threads = $user->threads()->visible()->with(['user', 'node'])->recent()->paginate();
        }
        return $threads;
    }

    public function getReplies(User $user)
    {
        $replies = $user->replies()->visible()->with(['thread'])->recent()->paginate();
        foreach ($replies as $key => $reply) {
            if ($reply->thread->status < 0) {
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
        if (Auth::check() && $user->id == Auth::id()) {
            $threads = $user->favorites()->with(['thread.user', 'thread.node'])->paginate();
        } else {
            $threads = [];
        }
        return $threads;
    }
}