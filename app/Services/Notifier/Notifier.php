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
use Hifone\Exceptions\HifoneException;
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
        //小红点 以下类型暂不计数
//        || thread_favorite || $type == 'thread_pin' || $type == 'reply_pin' || thread_mark_excellent
        if ($type == 'reply_like' || $type == 'thread_like' || $type == 'user_follow') {
            $toUser->increment('notification_system_count', 1);
        } elseif ($type == 'reply_reply' || $type == 'reply_mention') {
            $toUser->increment('notification_at_count', 1);
        } elseif ($type == 'thread_new_reply') {
            $toUser->increment('notification_reply_count', 1);
        }

        //消息推送
        $this->pushNotify($author, $toUser, $type, $object);

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
            //小红点
            if ($type == 'reply_mention') {
                $toUser->increment('notification_at_count');
            } elseif ($type == 'followed_user_new_thread') {
                $toUser->increment('notification_follow_count');
            }

            //消息推送 (暂不考虑thread_mention)
            if ($type == 'reply_mention' || $type == 'thread_mention') {
                $this->pushNotify($author, $toUser, $type, $object);
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
     * @param $type 类型映射
     * @param $object reply
     */
    protected function pushNotify($from, $to, $type, $object)
    {
        $message = $this->makeMessage($type, $object);
        $title = $this->makeTitle($from, $type);
        
        //友盟消息推送
        $data = array(
            'message' => $message,
            'type' => $type,
            'avatar' => $from->avatar_url,
            'title' => $title,
            'time' => date('Y-m-d H:i', strtotime('now')),
            'userId' => $from->id,
            'replyId' => ($object instanceof Thread) ? : $object->id,

            'msg_type' => '1',//推送消息类型 0.通知,1.消息
            'outline' => '',
            'uid' => $to->phicomm_id,
        );

        app('push')->push($data);
    }

    protected function makeTitle($operator, $typeStr)
    {
        switch ($typeStr){
            case 'thread_new_reply'://评论帖子
                return "【" . $operator->username . "】评论了你";
            case 'reply_reply'://回复
                return "【" . $operator->username . "】回复中提到了你";
            case 'reply_mention'://回复@我
                return "【" . $operator->username . "】回复中提到了你";
            case 'thread_mention'://帖子@我
                return "【" . $operator->username . "】帖子中提到了你";
            case 'user_follow'://关注用户
                return "【" . $operator->username . "】关注了你";
            case 'thread_like'://赞帖子
                return "【" . $operator->username . "】赞了你的帖子";
            case 'reply_like'://赞回复
                return "【" . $operator->username . "】赞了你的回复";
            case 'thread_favorite'://收藏（帖子）
                return "【" . $operator->username . "】收藏了你的帖子";
            case 'thread_pin'://置顶帖子
                return "【" . $operator->username . "】置顶了你的帖子";
            case 'reply_pin'://置顶评论回复
                return "【" . $operator->username . "】置顶你的回复";
            case 'thread_mark_excellent'://加精华
                return "【" . $operator->username . "】加精了你的帖子";
            default :
                throw new HifoneException("推送类型 $typeStr 不支持");
        }
    }

    protected function makeMessage($type, $object)
    {
        $message = ($object instanceof Thread) ? $object->title: (isset($object->body) ? $object->body : '');
        if ($object instanceof Thread) {
            $message = $object->title;
        } elseif ($object instanceof Reply) {
            $message = $object->body;
        } elseif ($object instanceof User) {
            $message = "查看详情";
        }

        return $message;
    }
}
