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
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Services\Filter\WordsFilter;
use DB;
use Input;
use Auth;
use Config;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReplyBll extends BaseBll
{
    public function createReply()
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        $thread = Thread::findOrFail(request('reply.thread_id'));
        if (!$thread->visible) {
            throw new HifoneException('该帖子已被删除');
        }
        $this->replyIsVisible();
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
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        $thread = Thread::findOrFail(request('reply.thread_id'));
        if (!$thread->visible) {
            throw new HifoneException('该帖子已被删除');
        }
        $this->replyIsVisible();
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

    public function auditReply($reply, WordsFilter $wordsFilter)
    {
        $badWord = '';
        $needManAudit = Config::get('setting.auto_audit', 0) == 0  || ($badWord = $wordsFilter->filterWord($reply->body)) || $this->isContainsImageOrUrl($reply->body);
        $reply->body = app('parser.at')->parse($reply->body);
        $reply->body = app('parser.emotion')->parse($reply->body);
        if ($needManAudit) {
            $reply->bad_word = $badWord;
            $msg = $this->getMsg($reply->reply_id, false);
        } else {
            $this->autoAudit($reply);
            $msg = $this->getMsg($reply->reply_id, true);
        }
        $reply->save();
        $reply = Reply::find($reply->id);
        $reply->load(['user', 'reply.user']);
        $reply['liked'] = Auth::check() ? Auth::user()->hasLikeReply($reply) : false;
        $reply['reported'] = Auth::check() ? Auth::user()->hasReportReply($reply) : false;
        return [
            'msg' => $msg,
            'reply' => $reply
        ];
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
            $reply->thread->updateIndex();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new HifoneException($e->getMessage());
        }
    }

    public function getMsg($reply_id, $isAutoAudit)
    {
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
        if ($reply->status <> Reply::VISIBLE) {
            throw new HifoneException('该评论已被删除');
        } else {
            $reply = Reply::with(['user', 'reply.user'])->find($reply->id);
            $reply['liked'] = Auth::check() ? Auth::user()->hasLikeReply($reply) : false;
            $reply['reported'] = Auth::check() ? Auth::user()->hasReportReply($reply) : false;
        }
        return $reply;
    }

    private function replyIsVisible()
    {
        if (!is_null(request('reply.reply_id'))) {
            $reply = Reply::find(request('reply.reply_id'));
            if ($reply == null || $reply->status <> Reply::VISIBLE) {
                throw new NotFoundHttpException('该评论已被删除');
            }
        }
    }
}