<?php
namespace Hifone\Handlers\Listeners\User;

use Carbon\Carbon;
use Hifone\Events\EventInterface;

class H5UserWasActiveHandler
{
    //登录状态的用户，触发请求操作时，记录时间，且每天只记录一次
    public function handle(EventInterface $event)
    {
        $user = $event->user;
        $user->update([
            'last_active_time' => Carbon::now(),
            'device' => 1,
        ]);
    }

}