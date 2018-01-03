<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Services\Notifier;

use Carbon\Carbon;
use Hifone\Http\Bll\BaseBll;
use Hifone\Models\Notification;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;

class Notifier
{
    protected $notifiedUsers = [];

    public function notify($type, User $author, User $toUser, $object)
    {
        if ($this->isNotified($author->id, $toUser->id, $object, $type)) {
            return;
        }
        $data = [
            'author_id'     => $author->id,
            'user_id'       => $toUser->id,
            'body'          => ($object instanceof Thread) ? '': (isset($object->body) ? $object->body : ''),
            'type'          => $type,
        ];
        if ($type == 'reply_like' || $type == 'thread_like' || $type == 'user_follow') {
            $toUser->increment('notification_system_count', 1);
        } elseif ($type == 'reply_reply' || $type == 'reply_mention') {
            $toUser->increment('notification_at_count', 1);

            //消息推送
            $this->push($author, $toUser, "1002", $data['body']);
        } elseif ($type == 'thread_new_reply') {
            $toUser->increment('notification_reply_count', 1);

            //消息推送
            $this->push($author, $toUser, "1001", $data['body']);
        }

        $object->notifications()->create($data);
    }

    public function batchNotify($type, User $author, $users, $object, $content = null)
    {
        foreach ($users as $user) {
            $toUser = (!$user instanceof User) ? $user->user : $user;
            if ($author->id == $toUser->id) {
                continue;
            }
            $data = [
                'author_id'     => $author->id,
                'user_id'       => $toUser->id,
                'body'          => $content,
                'type'          => $type,
            ];
            if ($type == 'thread_new_reply') {
                $toUser->increment('notification_reply_count');

                //消息推送
                $this->push($author, $toUser, "1001", $data['body']);
            } elseif ($type == 'reply_mention') {
                $toUser->increment('notification_at_count');

                //消息推送
                $this->push($author, $toUser, "1002", $data['body']);
            } elseif ($type == 'followed_user_new_thread') {
                $toUser->increment('notification_follow_count');
            }

            $object->notifications()->create($data);
        }
    }

    protected function isNotified($author_id, $user_id, $object, $type)
    {
        return $object->notifications()
                    ->forAuthor($author_id)
                    ->forUser($user_id)
                    ->ofType($type)->count();
    }

    // in case of a user get a lot of the same notification
    protected function removeDuplication($users)
    {
        $notYetNotifyUsers = [];
        foreach ($users as $follower) {
            $toUser = (!$follower instanceof User) ? $follower->user : $follower;

            if (!in_array($toUser->id, $this->notifiedUsers)) {
                $notYetNotifyUsers[] = $toUser;
                $this->notifiedUsers[] = $toUser->id;
            }
        }

        return $notYetNotifyUsers;
    }

    /**
     * @param $from
     * @param $to
     * @param $type thread_new_reply:1001; reply_reply, reply_mention:1002;
     * @param $content
     */
    protected function push($from, $to, $type, $content)
    {
        $data = array(
            'message' => $content,
            'type' => $type,
            'msg_type' => '0',//推送消息类型 0.通知,1.消息
            'outline' => substr($content, 0, 26),
            'title' => $from->username,
            'uid' => $to->phicomm_id,
            'url' => route('app.chat.message', ['user' => $from->id, 'scope' => 'after']),
        );

        (new BaseBll())->pushMessage($data);
    }
}
