<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/5
 * Time: 17:38
 */

namespace Hifone\Http\Bll;

use Auth;
use Config;
use Exception;
use Hifone\Commands\Follow\AddFollowCommand;
use Hifone\Models\Follow;
use Hifone\Models\User;

class FollowBll extends BaseBll
{
    public function followUser($user)
    {
        if ($user->id == Auth::id()) {
            throw new Exception('不能关注自己');
        }
        dispatch(new AddFollowCommand($user));

        return ['followed' => User::hasFollowUser($user)];
    }

    public function followThread($thread)
    {
        if ($thread->user->id == Auth::id()) {
            throw new Exception('自己的帖子无需关注');
        }
        dispatch(new AddFollowCommand($thread));

        return ['followed' => Auth::check() ? Auth::user()->hasFollowThread($thread) : false];
    }

    public function follows(User $user)
    {
        return $user->follows()->ofType(User::class)->recent()->with('follower')->paginate(15);
    }

    public function followers(User $user)
    {
        return $user->followers()->with('user')->recent()->paginate(15);
    }
}