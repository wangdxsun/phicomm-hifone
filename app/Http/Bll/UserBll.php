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
        $credits = Auth::user()->credits()->with(['rule' => function ($query) {
            $query->where('reward', '<>', 0);
        }])->recent()->paginate();

        return $credits;
    }

    public function getThreads(User $user)
    {
        $threads = $user->threads()->visible()->with(['user', 'node'])->recent()->get();

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