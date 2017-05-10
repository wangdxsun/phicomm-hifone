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
    public function watch()
    {
        return Notification::forUser(Auth::id())->watch()->recent()->paginate(15);
    }

    public function reply()
    {
        return Notification::forUser(Auth::id())->ofType('thread_new_reply')->recent()->paginate(15);
    }

    public function at()
    {
        return Notification::forUser(Auth::id())->at()->recent()->paginate(15);
    }

    public function message()
    {
        return [];
    }

    public function system()
    {
        return Notification::forUser(Auth::id())->system()->recent()->paginate(15);
    }
}