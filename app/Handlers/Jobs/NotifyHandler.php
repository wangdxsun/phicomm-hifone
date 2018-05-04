<?php

namespace Hifone\Handlers\Jobs;

use Hifone\Exceptions\HifoneException;
use Hifone\Jobs\Notify;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;

class NotifyHandler
{
    public function handle(Notify $notify)
    {
        //判断是否通知过
        if ($this->isNotified($notify->author->id, $notify->user->id, $notify->object, $notify->type)) {
            return;
        }
        //自己操作自己不通知
        if ($notify->author->id == $notify->user->id) {
            return;
        }

        if ($notify->type == 'reply_like' || $notify->type == 'thread_like' || $notify->type == 'user_follow'
            || $notify->type == 'thread_favorite' || $notify->type == 'thread_pin' || $notify->type == 'thread_mark_excellent' || $notify->type == 'reply_pin') {
            $notify->user->increment('notification_system_count', 1);
        } elseif ($notify->type == 'reply_reply' || $notify->type == 'reply_mention' || $notify->type == 'thread_mention') {
            $notify->user->increment('notification_at_count', 1);
        } elseif ($notify->type == 'thread_new_reply') {
            $notify->user->increment('notification_reply_count', 1);
        } elseif ($notify->type == 'followed_user_new_thread') {
            $notify->user->increment('notification_follow_count');
        }

        $data = [
            'author_id'     => $notify->author->id,
            'user_id'       => $notify->user->id,
            'body'          => $notify->content,
            'type'          => $notify->type,
        ];
        $notify->object->notifications()->create($data);

        //消息推送
        if ($notify->type <> 'followed_user_new_thread') {
            $this->pushNotify($notify->author, $notify->user, $notify->type, $notify->object);
        }
    }

    protected function isNotified($author_id, $user_id, $object, $type)
    {
        return $object->notifications()
            ->forAuthor($author_id)
            ->forUser($user_id)
            ->ofType($type)->count();
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
                return env('APP_URL').'/images/admin_avatar.png';
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