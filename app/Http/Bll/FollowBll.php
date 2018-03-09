<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/5
 * Time: 17:38
 */

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Commands\Follow\AddFollowCommand;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FollowBll extends BaseBll
{
    public function followUser($user)
    {
        if ($user->id == Auth::id()) {
            throw new HifoneException('不能关注自己');
        }
        dispatch(new AddFollowCommand($user));

        return ['followed' => User::hasFollowUser($user)];
    }

    public function followThread($thread)
    {
        if ($thread->status <> Thread::VISIBLE) {
            throw new HifoneException('该帖子已被删除', 410);
        }
        if ($thread->user->id == Auth::id()) {
            throw new HifoneException('自己的帖子无需关注');
        }
        dispatch(new AddFollowCommand($thread));

        return ['followed' => Auth::user()->hasFollowThread($thread)];
    }

    public function followNode($node)
    {
        dispatch(new AddFollowCommand($node));
        return ['followed' => Auth::user()->hasFollowNode($node)];
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