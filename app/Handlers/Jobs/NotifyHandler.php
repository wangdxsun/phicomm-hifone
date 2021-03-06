<?php

namespace Hifone\Handlers\Jobs;

use Hifone\Exceptions\HifoneException;
use Hifone\Jobs\Notify;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Question;
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

        /**
         * web和H5 红点逻辑
         * 评论	评论一个帖子、评论一个回答
         * 回复/@我	回复一个评论、回复@、帖子@、回复一个回复、提问被@、回答被@、回复被@
         * 问答	问题被回答、
         * 通知	赞了回答、赞了回复、提醒采纳、邀请回答、回答被采纳
         */
        if (in_array($notify->type, ['reply_like', 'thread_like', 'user_follow', 'thread_favorite', 'thread_pin', 'thread_mark_excellent', 'reply_pin', 'answer_like', 'comment_like', 'adopt_asap', 'user_invited', 'answer_adopted'])) {
            $notify->user->increment('notification_system_count', 1);

        } elseif (in_array($notify->type, ['reply_reply', 'reply_mention', 'thread_mention', 'question_mention', 'answer_mention', 'comment_mention', 'comment_new_comment'])) {
            $notify->user->increment('notification_at_count', 1);

        } elseif (in_array($notify->type, ['thread_new_reply', 'answer_new_comment'])) {
            $notify->user->increment('notification_reply_count', 1);

        } elseif (in_array($notify->type, ['followed_user_new_thread', 'followed_user_new_question'])) {
            $notify->user->increment('notification_follow_count', 1);

        } elseif (in_array($notify->type, ['question_new_answer'])) {
            $notify->user->increment('notification_qa_count', 1);
        }

        $data = [
            'author_id'     => $notify->author->id,
            'user_id'       => $notify->user->id,
            'body'          => $notify->content,
            'type'          => $notify->type,
        ];
        $notify->object->notifications()->create($data);

        //消息推送
        if ($notify->type == 'user_invited' || $notify->type == 'adopt_asap') {//邀请回答、尽快采纳 非静默方式
            $this->pushNotify($notify->author, $notify->user, $notify->type, $notify->object, '0');
        } elseif ($notify->type <> 'followed_user_new_thread' && $notify->type <> 'followed_user_new_question') {
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
     * @param $type string 类型映射
     * @param $object reply
     * @param $msg_type string 推送消息类型 0.通知，1.消息（默认）
     */
    protected function pushNotify($from, $to, $type, $object, $msg_type = '1')
    {
        //构造头像、标题、消息内容
        $avatar = $this->makeAvatar($from, $type);
        $title = $this->makeTitle($from, $type);
        $message = $this->makeMessage($type, $object);

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
        } elseif ($object instanceof Question) {
            $data['questionId'] = $object->id;
        } elseif ($object instanceof Answer) {
            $data['answerId'] = $object->id;
        } elseif ($object instanceof Comment) {
            $data['answerId'] = $object->answer_id;
        }
        //系统提醒尽快采纳user_id传0，管理员、系统采纳回答传0，其中系统操作userId本身即为0
        if ($type == 'answer_adopted') {
            //采纳回答消息传悬赏值
            $data['score'] = $object->question->score;
            //管理员采纳回答userId置为0
            if ($from->isAdmin()) {
                $data['userId'] = 0;
            }
        }

        $outline = $this->makeOutline($from, $object);

        app('push')->push($to->phicomm_id, $data, $outline, $msg_type);
    }

    protected function makeAvatar($operator, $typeStr)
    {
        switch ($typeStr){
            case 'thread_pin'://置顶帖子
            case 'reply_pin'://置顶评论回复
            case 'thread_mark_excellent'://加精华
            case 'adopt_asap'://尽快采纳
                return env('APP_URL').'/images/admin_avatar.png';
            case 'answer_adopted'://系统自动采纳
                if ($operator->isAdmin() || $operator->id == 0) {
                    return env('APP_URL').'/images/admin_avatar.png';
                }
            default :
                return $operator->avatar_url;
        }
    }

    protected function makeTitle($operator, $typeStr)
    {
        if (mb_strlen($operator->username) > 6) {
            $username = mb_substr($operator->username, 0, 6) . '...';
        } else {
            $username = $operator->username;
        }
        switch ($typeStr){
            case 'thread_new_reply'://评论帖子
                return "【" . $username . "】评论了你";
            case 'reply_reply'://回复
                return "【" . $username . "】回复了你";
            case 'reply_mention'://回复@我
                return "【" . $username . "】回复中提及了你";
            case 'thread_mention'://帖子@我
                return "【" . $username . "】帖子中提及了你";
            case 'user_follow'://关注用户
                return "【" . $username . "】关注了你";
            case 'thread_like'://赞帖子
                return "【" . $username . "】赞了你的帖子";
            case 'reply_like'://赞回复
                return "【" . $username . "】赞了你的评论";
            case 'thread_favorite'://收藏（帖子）
                return "【" . $username . "】收藏了你的帖子";
            case 'thread_pin'://置顶帖子
                return "【管理员】置顶了你的帖子";
            case 'reply_pin'://置顶评论回复
                return "【管理员】置顶你的评论";
            case 'thread_mark_excellent'://加精华
                return "【管理员】加精了你的帖子";

            case 'question_mention'://提问中@我
                return "【" . $username . "】问题中提及了你";
            case 'answer_mention'://回答中@我
                return "【" . $username . "】回答中提及了你";
            case 'comment_mention'://回复中@我
                return "【" . $username . "】回复中提及了你";
            case 'question_new_answer'://回答提问
                return "【" . $username . "】回答了你的问题";
            case 'answer_new_comment':
                return "【" . $username . "】评论了你的回答";
            case 'comment_new_comment':
                return "【" . $username . "】回复了你的评论";
            case 'answer_like'://点赞回答
                return "【" . $username . "】赞了你的回答";
            case 'comment_like'://点赞回复
                return "【" . $username . "】赞了你的评论";
            case 'user_invited'://邀请回答
                return "【" . $username . "】邀请你回答问题";
            case 'adopt_asap'://尽快采纳
                return "【系统】提醒你尽快采纳";
            case 'answer_adopted'://回答被采纳（区分用户、管理员、系统）
                if ($operator->isAdmin() || $operator->id == 0) {
                    return "【系统】采纳了你的回答";
                } else {
                    return "【" . $username . "】采纳了你的回答";
                }
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
        } elseif ($object instanceof Question) {
            $message = $object->title;
        } elseif ($object instanceof Answer) {
            $message = $object->body;
        } elseif ($object instanceof Comment) {
            $message = $object->body;
        }
        $message = app('parser.emotion')->reverseParseEmotionAndImage($message);
        $message = str_replace(["\n", "\r", "\r\n"], '', $message);
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
        } elseif ($object instanceof Question) {
            $outline = $object->title;
        } elseif ($object instanceof Answer) {
            $outline = $object->body;
        } elseif ($object instanceof Comment) {
            $outline = $object->body;
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

            case 'question_mention'://提问中@我 跳问题详情
                return '2001';
            case 'answer_mention'://回答中@我 跳回答详情
                return '2002';
            case 'comment_mention'://回复中@我 跳回答详情
                return '2003';
            case 'question_new_answer'://回答提问 跳回答详情
                return '2004';
            case 'answer_new_comment'://评论回答 跳回答详情
                return '2005';
            case 'comment_new_comment'://回复评论 跳回答详情
                return '2006';
            case 'answer_like'://赞回答 跳回答详情
                return '2007';
            case 'comment_like'://赞回复 跳回复详情
                return '2008';
            case 'user_invited'://邀请回答 跳问题详情
                return '2009';
            case 'adopt_asap'://尽快采纳 跳问题详情
                return '2010';
            case 'answer_adopted'://采纳回答 跳回答详情
                return '2011';
            default :
                throw new HifoneException("推送类型 $typeStr 不支持");
        }
    }

}