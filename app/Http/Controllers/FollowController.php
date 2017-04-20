<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers;

use Auth;
use Hifone\Commands\Follow\AddFollowCommand;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Illuminate\Support\Facades\Response;

class FollowController extends Controller
{
    public function createOrDelete(Thread $thread)
    {
        if ($thread->user->id == Auth::id()) {
            return ['status' => -1, 'msg' => '自己的帖子无需关注'];
        }

        dispatch(new AddFollowCommand($thread));

        return Response::json(['status' => 1]);
    }

    public function createOrDeleteUser(User $user)
    {
        if ($user->id == Auth::user()->id) {
            return ['status' => -1, 'msg' => '不能关注自己'];
        }

        dispatch(new AddFollowCommand($user));

        return Response::json(['status' => 1]);
    }
}
