<?php
namespace  Hifone\Handlers\Listeners\User;

use Carbon\Carbon;
use Hifone\Events\EventInterface;

class UserWasLoggedInHandler
{
    //完成登陆后记录最后登录时间的功能
    public function handle(EventInterface $event)
    {
        $user = $event->user;
        $user->update(['last_visit_time' => Carbon::now()]);
    }
}