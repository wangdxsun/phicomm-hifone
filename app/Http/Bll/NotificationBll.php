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
        $notifications = Notification::forUser(Auth::id())->watch()->recent()->with(['object'])->paginate();
        foreach ($notifications as $key => &$notification) {
            if (!$notification->object->visible) {
                unset($notifications[$key]);
            } else {
                $notification->object->node;
                $notification->object->user;
            }
        }
        Auth::user()->notification_follow_count = 0;
        Auth::user()->save();

        return $notifications;
    }

    public function reply()
    {
        $notifications = Notification::forUser(Auth::id())->ofType('thread_new_reply')->recent()->with(['object', 'author'])->paginate();
        foreach ($notifications as $key => &$notification) {
            if (empty($notification->object) || !$notification->object->visible || !$notification->object->thread->visible) {//兼容回收站帖子-1状态的老数据
                unset($notifications[$key]);
            }
        }
        Auth::user()->notification_reply_count = 0;
        Auth::user()->save();

        return $notifications;
    }

    public function at()
    {
        $notifications = Notification::forUser(Auth::id())->at()->recent()->with(['object', 'author'])->paginate();
        foreach ($notifications as $key => &$notification) {
            if (!$notification->object->visible || ($notification->object_type == 'Hifone\Models\Reply' && !$notification->object->thread->visible)) {
                unset($notifications[$key]);
            }
        }
        Auth::user()->notification_at_count = 0;
        Auth::user()->save();

        return $notifications;
    }

    public function system()
    {
        $notifications = Notification::forUser(Auth::id())->system()->recent()->with(['object', 'author'])->paginate();
        foreach ($notifications as $key => &$notification) {
            if ($notification->type <> 'user_follow' && !$notification->object->visible) {
                unset($notifications[$key]);
            }
            if ($notification->type == 'reply_like' && !$notification->object->thread->visible) {
                unset($notifications[$key]);
            }
        }
        Auth::user()->notification_system_count = 0;
        Auth::user()->save();

        return $notifications;
    }

}