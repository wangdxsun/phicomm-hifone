<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/4/25
 * Time: 15:27
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Commands\Follow\AddFollowCommand;
use Hifone\Models\User;

class FollowController extends AbstractApiController
{
    public function user(User $user)
    {
        if ($user->id == \Auth::id()) {
            throw new \Exception('不能关注自己');
        }

        dispatch(new AddFollowCommand($user));

        return ['status' => 1];
    }
}