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
use Illuminate\Support\Facades\DB;
use Input;
use Auth;

class ReplyBll extends BaseBll
{
    public function createReply()
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new \Exception('对不起，你已被管理员禁止发言');
        }
        $replyData = request('reply');

        $reply = dispatch(new AddReplyCommand(
            $replyData['body'],
            Auth::id(),
            $replyData['thread_id']
        ));
        return $reply;
    }

    public function replyPassAutoAudit($reply)
    {
        $thread = $reply->thread;
        $thread->last_reply_user_id = $reply->user_id;
        $thread->save();
        event(new ReplyWasAddedEvent($reply));
        event(new RepliedWasAddedEvent($reply->user, $thread->user));

        DB::beginTransaction();
        try {
            $reply->thread->node->increment('reply_count', 1);//版块回帖数+1
            $reply->thread->subNode->increment('reply_count', 1);//子版块回帖数+1
            $reply->thread->increment('reply_count', 1);
            $reply->thread->updateIndex();
            $reply->user->increment('reply_count', 1);

            $reply->status = 0;
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
            throw new \Exception('系统错误！');
        }
    }
}