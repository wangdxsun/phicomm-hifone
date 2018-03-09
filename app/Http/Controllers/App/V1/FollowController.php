<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/20
 * Time: 16:00
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\FollowBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Node;
use Hifone\Models\Thread;
use Hifone\Models\User;

class FollowController extends AppController
{
    public function user(User $user, FollowBll $followBll)
    {
        return $followBll->followUser($user);
    }

    public function thread(Thread $thread, FollowBll $followBll)
    {
        return $followBll->followThread($thread);
    }

    //关注板块
    public function node(Node $node, FollowBll $followBll)
    {
        return $followBll->followNode($node);
    }
}