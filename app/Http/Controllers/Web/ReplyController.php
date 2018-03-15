<?php

namespace Hifone\Http\Controllers\Web;

use Auth;
use Config;
use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\ReplyBll;
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
        $result = $replyBll->auditReply($reply, $wordsFilter);

        Redis::set($redisKey, $redisKey);
        Redis::expire($redisKey, 10);//设置评论回复防灌水倒计时

        return $result;
    }
}