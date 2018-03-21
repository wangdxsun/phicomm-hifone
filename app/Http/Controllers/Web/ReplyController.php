<?php

namespace Hifone\Http\Controllers\Web;

use Auth;
use Config;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\ReplyBll;
use Hifone\Models\Reply;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Support\Facades\Redis;

class ReplyController extends WebController
{
    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        //防灌水
        $redisKey = 'reply_user:' . Auth::id();
        if (Redis::exists($redisKey)) {
            throw new HifoneException('回复间隔时间短，请稍后再试');
        }

        $reply = $replyBll->createReply();
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

        Redis::set($redisKey, $redisKey);
        Redis::expire($redisKey, 10);//设置评论回复防灌水倒计时

        return $result;
    }
}