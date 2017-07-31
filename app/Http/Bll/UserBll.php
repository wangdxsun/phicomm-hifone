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
        //查看自己或管理员查看帖子，包括自己未审核通过的贴子
        if ($user->id == Auth::id() || Auth::user()->role == '创始人' || Auth::user()->role == '管理员') {
            $threads = $user->threads()->with(['user', 'node'])->recent()->get();
        } else {
            $threads = $user->threads()->visible()->with(['user', 'node'])->recent()->get();
        }
        return $threads;
    }

    public function getReplies(User $user)
    {
        $replies = $user->replies()->visible()->with(['thread'])->recent()->get();
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
}