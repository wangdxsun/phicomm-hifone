<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2018/1/9
 * Time: 14:09
 */

namespace Hifone\Handlers\Listeners\User;

use Carbon\Carbon;
use Hifone\Events\EventInterface;

class WebUserWasActiveHandler
{
    //登录状态的用户，触发请求操作时，记录时间，且每天只记录一次
    public function handle(EventInterface $event)
    {
        $user = $event->user;
        $user->update([
            'last_active_time_web' => Carbon::now()->toDateTimeString(),
            'device' => 2,
        ]);
    }

}