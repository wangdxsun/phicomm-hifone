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
//        || $type == 'reply_pin'
        if ($type == 'reply_like' || $type == 'thread_like' || $type == 'user_follow'
        || $type == 'thread_favorite' || $type == 'thread_pin' || $type == 'thread_mark_excellent') {
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
        $avatar = $this->makeAvatar($from, $type);
        $title = $this->makeTitle($from, $type);

        $typeNum = $this->getType($type);
        $data = [
            "content" => mb_substr($message, 0, 100),
            "type" => $typeNum,
            "source" => '1',
            "producer" => '2',
            "isBroadcast" => '0',
            "isMulticast" => '0',
            "avatar" => $avatar,
            "title" => $title,
            "time" => date('Y-m-d H:i', strtotime('now')),
            "userId" => $from->id,
        ];
        if ($object instanceof Thread) {
            $data['threadId'] = $object->id;
        } elseif ($object instanceof Reply) {
            $data['threadId'] = $object->thread_id;
            $data['replyId'] = $object->id;
        }
        $outline = $this->makeOutline($from, $object);

        app('push')->push($to->phicomm_id, $data, $outline);
    }

    protected function makeAvatar($operator, $typeStr)
    {
        switch ($typeStr){
            case 'thread_pin'://置顶帖子
            case 'reply_pin'://置顶评论回复
            case 'thread_mark_excellent'://加精华
                return request()->getSchemeAndHttpHost().'/images/admin_avatar.png';
            default :
                return $operator->avatar_url;
        }
    }

    protected function makeTitle($operator, $typeStr)
    {
        switch ($typeStr){
            case 'thread_new_reply'://评论帖子
                return "【" . $operator->username . "】评论了你";
            case 'reply_reply'://回复
                return "【" . $operator->username . "】回复了你";
            case 'reply_mention'://回复@我
                return "【" . $operator->username . "】回复中提到了你";
            case 'thread_mention'://帖子@我
                return "【" . $operator->username . "】帖子中提到了你";
            case 'user_follow'://关注用户
                return "【" . $operator->username . "】关注了你";
            case 'thread_like'://赞帖子
                return "【" . $operator->username . "】赞了你的帖子";
            case 'reply_like'://赞回复
                return "【" . $operator->username . "】赞了你的评论";
            case 'thread_favorite'://收藏（帖子）
                return "【" . $operator->username . "】收藏了你的帖子";
            case 'thread_pin'://置顶帖子
                return "【管理员】置顶了你的帖子";
            case 'reply_pin'://置顶评论回复
                return "【管理员】置顶你的评论";
            case 'thread_mark_excellent'://加精华
                return "【管理员】加精了你的帖子";
            default :
                throw new HifoneException("推送类型 $typeStr 不支持");
        }
    }

    protected function makeMessage($type, $object)
    {
        if ($object instanceof Thread) {
            $message = $object->title;
        } elseif ($object instanceof Reply) {
            $message = $object->body;
        } elseif ($object instanceof User) {
            $message = "查看详情";
        }
        $message = app('parser.emotion')->reverseParseEmotionAndImage($message);
        $message = strip_tags($message);

        return $message;
    }

    protected function makeOutline($from, $object)
    {
        if ($object instanceof Thread) {
            $outline = $object->title;
        } elseif ($object instanceof Reply) {
            $outline = $object->body;
        } elseif ($object instanceof User) {
            $outline = "【" . $from->username . "】关注了你";
        }
        $outline = app('parser.emotion')->reverseParseEmotionAndImage($outline);
        $outline = strip_tags($outline);
        $outline = mb_substr($outline, 0, 26);

        return $outline;
    }

    /**
     * @param $typeStr
     *    1001 评论
     *    1002 回复/帖子@我/回复@我
     *    1003 收到的关注
     *    1004 私信
     *    1005 收到的赞（赞帖子、赞评论/回复）
     *    1006 收到收藏
     *    1007 管理员置顶（帖子、评论/回复）
     *    1008 管理员加精华
     * @return string
     * @throws HifoneException
     */
    protected function getType($typeStr)
    {
        switch ($typeStr){
            case 'thread_new_reply'://评论帖子 跳当前评论
                return '1001';
            case 'reply_reply'://回复 跳当前评论
                return '1002';
            case 'reply_mention'://回复@我 跳当前评论
                return '1002';
            case 'thread_mention'://帖子@我 跳帖子详情
                return '1002';
            case 'user_follow'://关注用户 跳粉丝列表
                return '1003';
            case 'chat'://私信 跳聊天记录
                return '1004';
            case 'thread_like'://赞帖子 跳帖子详情
            case 'reply_like'://赞回复 跳当前评论
                return '1005';
            case 'thread_favorite'://收藏（帖子） 跳帖子详情
                return '1006';
            case 'thread_pin'://置顶帖子 跳帖子详情
            case 'reply_pin'://置顶评论 跳当前评论
                return '1007';
            case 'thread_mark_excellent'://加精华 跳帖子详情
                return '1008';
            default :
                throw new HifoneException("推送类型 $typeStr 不支持");
        }
    }
}
