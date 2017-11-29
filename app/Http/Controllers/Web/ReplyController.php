<?php

namespace Hifone\Http\Controllers\Web;

use Config;
use Hifone\Http\Bll\ReplyBll;
use Hifone\Services\Filter\WordsFilter;

class ReplyController extends WebController
{
    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        $reply = $replyBll->createReply();
        $result = $replyBll->auditReply($reply, $wordsFilter);

        return $result;
    }
}