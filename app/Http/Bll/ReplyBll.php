<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:20
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Commands\Reply\AddReplyCommand;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Reply\ReplyWasAddedEvent;
use Hifone\Events\Reply\ReplyWasAuditedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Node;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Services\Filter\WordsFilter;
use DB;
use Input;
use Auth;
use Config;

class ReplyBll extends BaseBll
{
    public function createReply()
    {
        $this->checkPermission();
        $this->checkThread(request('reply.thread_id'));
        $this->replyIsVisible(request('reply.reply_id'));
        $replyData = request('reply');
        $replyData['body'] = e($replyData['body']);
        $images = '';
        if (Input::has('images')) {
            foreach ($replyImages = Input::get('images') as $image) {
                $upload = dispatch(new UploadBase64ImageCommand($image));
                $images .= "<img src='{$upload["filename"]}'/>";
            }
        }

        $reply = dispatch(new AddReplyCommand(
            $replyData['body'],
            Auth::id(),
            $replyData['thread_id'],
            array_get($replyData, 'reply_id'),
            $images
        ));
        return $reply;
    }

    public function createReplyApp()
    {
        $this->checkPermission();
        $this->checkThread(request('reply.thread_id'));
        $this->replyIsVisible(request('reply.reply_id'));
        $replyData = request('reply');
        $replyData['body'] = e($replyData['body']);
        $images = '';
        if (Input::has('images')) {
            foreach ($replyImages = json_decode(Input::get('images'), true) as $image) {
                $images.= "<img src='".$image['image']."'/>";
            }
        }

        $replyTemp = dispatch(new AddReplyCommand(
            $replyData['body'],
            Auth::id(),
            $replyData['thread_id'],
            array_get($replyData, 'reply_id'),
            $images
        ));
        $reply = Reply::find($replyTemp->id);
        return $reply->load('user', 'reply.user');
    }

    //用户反馈回帖逻辑
    public function createFeedbackApp()
    {
        $this->checkPermission();
        $feedback_thread_id = Node::find(request('reply.node_id'))->feedback_thread_id;
        $this->checkThread($feedback_thread_id);
        $replyData = request('reply');
        $replyData['body'] = e($replyData['body']);
        $replyData['thread_id'] = $feedback_thread_id;
        $images = '';
        if (Input::has('images')) {
            foreach ($replyImages = json_decode(Input::get('images'), true) as $image) {
                $images.= "<img src='".$image['image']."'/>";
            }
        }
        $channel = Reply::FEEDBACK;
        $dev_info = $replyData['dev_info'];
        $contact  = isset($replyData['contact']) ? $replyData['contact'] : null;

        $replyTemp = dispatch(new AddReplyCommand(
            $replyData['body'],
            Auth::id(),
            $replyData['thread_id'],
            array_get($replyData, 'reply_id'),
            $images,
            $channel,
            $dev_info,
            $contact
        ));
        $reply = Reply::find($replyTemp->id);
        return $reply->load('user', 'reply.user');
    }

    public function auditReply($reply, WordsFilter $wordsFilter)
    {
        $badWord = '';
        $needManAudit = Config::get('setting.auto_audit', 0) == 0  || ($badWord = $wordsFilter->filterWord($reply->body)) || $this->isContainsImageOrUrl($reply->body);
        $reply->body = app('parser.link')->parse($reply->body);
        $reply->body = app('parser.at')->parse($reply->body);
        $reply->body = app('parser.emotion')->parse($reply->body);
        if ($needManAudit) {
            $reply->bad_word = $badWord;
        } else {
            $this->autoAudit($reply);
        }
        $reply->save();

        return $reply;
    }

    public function autoAudit($reply)
    {
        $thread = $reply->thread;
        $thread->last_reply_user_id = $reply->user_id;
        $thread->save();
        event(new ReplyWasAddedEvent($reply));
        event(new RepliedWasAddedEvent($reply->user, $thread->user));

        DB::beginTransaction();
        try {
            $reply->update(['status' => Reply::VISIBLE]);
            $reply->thread->node->increment('reply_count', 1);//版块回帖数+1
            $reply->thread->subNode->increment('reply_count', 1);//子版块回帖数+1
            $reply->thread->update(['reply_count' => $reply->thread->replies()->visible()->count()]);
            $reply->user->update(['reply_count' => $reply->user->replies()->visibleAndDeleted()->count()]);

            $this->updateOpLog($reply, '自动审核通过');
            //把当前回复的创建时间和回复所属的帖子的修改时间进行比对
            //如果回复创建时间更新，则替换到帖子修改时间。否则，什么也不做。
            if ($reply->created_at > $reply->thread->updated_at) {
                $reply->thread->updated_at = $reply->created_at;
                $reply->thread->save();
            }

            event(new ReplyWasAuditedEvent($reply));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new HifoneException($e->getMessage());
        }
    }

    public function getMsg($reply_id, $isAutoAudit, $channel = Reply::REPLY)
    {
        if ($channel == Reply::FEEDBACK) {
            return $msg = '提交成功';
        }
        if (!$isAutoAudit) {
            if ($reply_id) {
                return $msg = '回复已提交，待审核';
            } else {
                return $msg = '评论已提交，待审核';
            }
        } else {
            if ($reply_id) {
                return $msg = '回复成功';
            } else {
                return $msg = '评论成功';
            }
        }

    }

    public function showReply(Reply $reply)
    {
        //评论状态APP根据status判断
        $reply = Reply::with(['user', 'reply.user'])->find($reply->id);
        $reply['liked'] = Auth::check() ? Auth::user()->hasLikeReply($reply) : false;
        $reply['reported'] = Auth::check() ? Auth::user()->hasReportReply($reply) : false;

        return $reply;
    }

    private function replyIsVisible($replyId)
    {
        if (!empty($replyId)) {
            $reply = Reply::find($replyId);
            if ($reply == null || $reply->status <> Reply::VISIBLE) {
                throw new HifoneException('该评论已被删除');
            }
        }
    }

    private function checkThread($threadId)
    {
        $thread = Thread::find($threadId);
        if (is_null($thread)) {
            throw new HifoneException('帖子不存在');
        } elseif ($thread->status <> Thread::VISIBLE) {
            throw new HifoneException('该帖子已被删除', 410);
        }
    }

    private function checkPermission()
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
    }
}