<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 20:53
 */

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Models\Notification;

class NotificationBll extends BaseBll
{
    public function thread()
    {
        return Auth::user()->notifications()->ofType('followed_user_new_thread')->recent()->paginate(15);
    }

    public function reply()
    {
        return Auth::user()->notifications()->ofType('thread_new_reply')->recent()->paginate(15);
    }

    public function at()
    {
        return Auth::user()->notifications()->at()->recent()->paginate(15);
    }

    public function message()
    {

    }

    public function system()
    {
        return Auth::user()->notifications()->system()->recent()->paginate(15);
    }
}