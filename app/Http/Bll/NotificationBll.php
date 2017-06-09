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
        return Notification::forUser(Auth::id())->watch()->recent()->with(['object', 'author'])->get();
    }

    public function reply()
    {
        $notifications = Notification::forUser(Auth::id())->ofType('thread_new_reply')->recent()->with(['author'])->get();
        foreach ($notifications as &$notification) {
            $notification->object->thread;
        }

        return $notifications;
    }

    public function at()
    {
        $notifications = Notification::forUser(Auth::id())->at()->recent()->with(['author'])->get();
        foreach ($notifications as $notification) {
            if ($notification->object) {
                $notification->object->thread;
            }
        }

        return $notifications;
    }

    public function system()
    {
        $notifications = Notification::forUser(Auth::id())->system()->recent()->with(['object', 'author'])->get();

        return $notifications;
    }
}