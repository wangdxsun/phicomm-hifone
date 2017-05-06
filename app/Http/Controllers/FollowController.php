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

use Hifone\Http\Bll\FollowBll;
use Hifone\Models\Thread;
use Hifone\Models\User;

class FollowController extends Controller
{
    public function createOrDelete(Thread $thread, FollowBll $followBll)
    {
        $followBll->followThread($thread);

        return ['status' => 1];
    }

    public function createOrDeleteUser(User $user, FollowBll $followBll)
    {
        $followBll->followUser($user);

        return ['status' => 1];
    }
}
