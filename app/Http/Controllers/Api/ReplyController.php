<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:22
 */

namespace Hifone\Http\Controllers\Api;

use Auth;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Reply\ReplyWasAddedEvent;
use Hifone\Events\Reply\ReplyWasAuditedEvent;
use Hifone\Http\Bll\ReplyBll;
use Hifone\Http\Controllers\Controller;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReplyController extends Controller
{
    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        $reply = $replyBll->createReply();
        if (Str::contains($reply->body,['<img']) || Str::contains($reply->body,['<a'])) {
            //回复中包含图片或者链接，都需要审核
            return success('发表成功，待审核');
        } else if (($wordsFilter->filterWord($reply->body))) {
            //自动审核未通过，需要人工审核
            return success('发表成功，待审核');
        }else {
            $thread = $reply->thread;
            $thread->last_reply_user_id = $reply->user_id;
            $thread->save();
            event(new ReplyWasAddedEvent($reply));
            event(new RepliedWasAddedEvent($reply->user, $thread->user));

            DB::beginTransaction();
            try {
                $reply->thread->node->increment('reply_count', 1);//版块回帖数+1
                $reply->thread->increment('reply_count', 1);
                $reply->user->increment('reply_count', 1);

                $reply->status = 0;
                $this->updateOpLog($reply, '审核通过');

                //把当前回复的创建时间和回复所属的帖子的修改时间进行比对
                //如果回复创建时间更新，则替换到帖子修改时间。否则，什么也不做。
                if ($reply->created_at > $reply->thread->updated_at) {
                    $reply->thread->updated_at = $reply->created_at;
                    $reply->thread->save();
                }

                event(new ReplyWasAuditedEvent($reply));
                DB::commit();
            } catch (ValidationException $e) {
                DB::rollback();
                throw new \Exception($e);
            }
            return [
                'msg' => '审核通过，发表成功！',
                'reply' => $reply
            ];
        }

    }
}