<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/24
 * Time: 11:15
 */

namespace Hifone\Http\Controllers\App\V1;

use Auth;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\ReplyBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Reply;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Support\Facades\Redis;
use Auth;

class ReplyController extends AppController
{
    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        //防灌水
        $redisKey = 'reply_user:' . Auth::id();
        if (Redis::exists($redisKey)) {
            throw new HifoneException('回复间隔时间短，请稍后再试');
        }
        Redis::set($redisKey, $redisKey);
        Redis::expire($redisKey, 10);//设置评论回复防灌水倒计时
        $reply = $replyBll->createReplyApp();
        $reply = $replyBll->auditReply($reply, $wordsFilter);
        $reply->load(['user', 'reply.user']);
        $reply['liked'] = Auth::check() ? Auth::user()->hasLikeReply($reply) : false;
        $reply['reported'] = Auth::check() ? Auth::user()->hasReportReply($reply) : false;
        if ($reply->status == Reply::VISIBLE) {
            $msg = $reply->reply_id ? '回复成功' : '评论成功';
        } else {
            $msg = $reply->reply_id ? '回复已提交，待审核' : '评论已提交，待审核';
        }
        $result = [
            'msg' => $msg,
            'reply' => $reply,
        ];

        return $result;
    }

    public function show(Reply $reply, ReplyBll $replyBll)
    {
        $reply = $replyBll->showReply($reply);

        return $reply;
    }

    public function feedback(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        $this->validate(request(), [
            'reply.body'    => 'required|min:1|max:800',
            'reply.node_id' => 'required',
        ], [
            'reply.node_id.required' => '请选择系列',
            'reply.body.required'    => '请输入反馈或建议',
            'reply.body.min'         => '请输入反馈或建议',
            'reply.body.max'         => '详情最多800个字符',
        ]);
        $reply = $replyBll->createFeedbackApp();
        $result = $replyBll->auditReply($reply, $wordsFilter);
        return $result;
    }

}