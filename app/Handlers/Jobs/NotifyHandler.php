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
        if (null == $notify->type) {
            return;
        }
        //判断是否通知过
        if ($this->isNotified($notify->author->id, $notify->user->id, $notify->object, $notify->type)) {
            return;
        }
        //自己操作自己不通知
        if ($notify->author->id == $notify->user->id) {
            return;
        }

        //web和H5 红点逻辑
        if (in_array($notify->type, ['reply_like', 'thread_like', 'user_follow', 'thread_favorite', 'thread_pin', 'thread_mark_excellent', 'reply_pin', 'adopt_asap'])) {
            $notify->user->increment('notification_system_count', 1);
        } elseif (in_array($notify->type, ['reply_reply', 'reply_mention', 'thread_mention', 'question_mention', 'answer_mention', 'comment_mention'])) {
            $notify->user->increment('notification_at_count', 1);
        } elseif ($notify->type == 'thread_new_reply') {
            $notify->user->increment('notification_reply_count', 1);
        } elseif (in_array($notify->type, ['followed_user_new_thread', 'followed_user_new_question'])) {
            $notify->user->increment('notification_follow_count');
        } elseif (in_array($notify->type, ['user_invited', 'answer_adopted', 'question_new_answer', 'answer_new_comment', 'comment_new_comment', ''])) {
            $notify->user->increment('notification_qa_count');
        }

        $data = [
            'author_id'     => $notify->author->id,
            'user_id'       => $notify->user->id,
            'body'          => $notify->content,
            'type'          => $notify->type,
        ];
        $notify->object->notifications()->create($data);

        //todo user_invited类型的消息处理，pusher方法调用第四个参数$msg_type传0表示通知

        //消息推送
        if ($notify->type <> 'followed_user_new_thread' && $notify->type <> 'followed_user_new_question') {
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
            case 'question_mention'://提问中@我
                return "【" . $operator->username . "】提问中提到了你";
            case 'answer_mention'://回答中@我
                return "【" . $operator->username . "】回答中提到了你";
            case 'comment_mention'://回复中@我
                return "【" . $operator->username . "】回复中提到了你";
            case 'question_new_answer'://回答提问
                return "【" . $operator->username . "】回答了你的提问";
            case 'answer_new_comment':
                return "【" . $operator->username . "】评论了你的回答";
            case 'comment_new_comment':
                return "【" . $operator->username . "】回复了你评论";
            case 'answer_like'://点赞回答
                return "【" . $operator->username . "】赞了你的回答";
            case 'comment_like'://点赞回复
                return "【" . $operator->username . "】赞了你的回复";
            default :
                throw new HifoneException("推送类型 $typeStr 不支持");
        }
    }

    protected function makeMessage($type, $object)
    {
        $message='';
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
        $outline = '';
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
            case 'question_mention'://提问中@我
                return '1009';
            case 'answer_mention'://回答中@我
                return '1010';
            case 'comment_mention'://回复中@我
                return '1011';
            case 'question_new_answer'://回答提问
                return '1012';
            case 'answer_new_comment'://评论回答
                return '1013';
            case 'comment_new_comment'://回复评论
                return '1014';
            case 'answer_like':
                return '1015';
            case 'comment_like':
                return '1016';
            default :
                throw new HifoneException("推送类型 $typeStr 不支持");
        }
    }

}