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
        $notifications = Notification::forUser(Auth::id())->watch()->whereHas('thread', function ($query) {
            $query->visibleAndDeleted();
        })->with(['thread.user', 'thread.node'])->recent()->paginate();
        Auth::user()->notification_follow_count = 0;
        Auth::user()->save();

        return $notifications;
    }

    public function reply()
    {
        $notifications = Notification::forUser(Auth::id())->reply()->whereHas('reply', function ($query) {
            $query->visibleAndDeleted();
        })->whereHas('reply.thread', function ($query) {
            $query->visibleAndDeleted();
        })->with(['reply.thread', 'reply.user', 'reply.reply.user'])->recent()->paginate();
        Auth::user()->notification_reply_count = 0;
        Auth::user()->save();

        return $notifications;
    }

    //web用，App走推送不用，H5待升级支持thread_mention
    public function at()
    {
        $notifications = Notification::forUser(Auth::id())->at()->orWhereHas('thread', function ($query) {
            $query->visibleAndDeleted();
        })->with(['thread.user', 'thread.node'])->whereHas('reply', function ($query) {
            $query->visibleAndDeleted();
        })->whereHas('reply.thread', function ($query) {
            $query->visibleAndDeleted();
        })->with(['reply.thread', 'reply.user', 'reply.reply.user'])->recent()->paginate();
        Auth::user()->notification_at_count = 0;
        Auth::user()->save();

        return $notifications;
    }

    public function system()
    {
        $notifications = Notification::forUser(Auth::id())->system()->recent()->with(['object', 'author'])->paginate();
        foreach ($notifications as $notification) {
            if ($notification->type == 'reply_like' || $notification->type == 'reply_pin') {
                $notification->object->thread;
                if ($notification->object->reply) {
                    $notification->object->reply->user;
                }
            }
        }
        Auth::user()->notification_system_count = 0;
        Auth::user()->save();

        return $notifications;
    }

}