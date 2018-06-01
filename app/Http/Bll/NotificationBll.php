<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 20:53
 */

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Notification;
use Hifone\Models\Question;
use Hifone\Models\Reply;
use Hifone\Models\Thread;

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

    //好友圈 thread和question的通知
    public function moment()
    {
        $moments = Notification::forUser(Auth::id())->moment()->with(['object'])->recent()->paginate();
        foreach ($moments as $moment) {
            if ($moment->object instanceof Thread) {
                $moment->object->load(['user', 'node']);
            } elseif ($moment->object instanceof Question) {
                $moment->object->load(['user', 'tags']);
            }
        }
        Auth::user()->notification_follow_count = 0;
        Auth::user()->save();

        return $moments;
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
        $notifications = Notification::forUser(Auth::id())->at()->whereHas('reply', function ($query) {
            $query->visibleAndDeleted();
        })->whereHas('reply.thread', function ($query) {
            $query->visibleAndDeleted();
        })->with(['reply.thread', 'reply.user', 'reply.reply.user'])->recent()->paginate();
        Auth::user()->notification_at_count = 0;
        Auth::user()->save();

        return $notifications;
    }

    public function atWithQA()
    {
        $notifications = Notification::forUser(Auth::id())->atWithQA()->with('object')->recent()->paginate();
        foreach ($notifications as $notification) {
            if ($notification->object instanceof Reply) {
                $notification->object->load(['thread', 'user', 'reply.user']);
            } elseif ($notification->object instanceof Question) {
                $notification->object->load(['user', 'tags']);
            } elseif ($notification->object instanceof Answer) {
                $notification->object->load(['user', 'question']);
            } elseif ($notification->object instanceof Comment) {
                $notification->object->load(['user', 'answer']);
            }
        }
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
                $notification->object->user;
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