<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/24
 * Time: 11:15
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\ReplyBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Reply;
use Hifone\Services\Filter\WordsFilter;

class ReplyController extends AppController
{
    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        $reply = $replyBll->createReplyApp();
        $result = $replyBll->auditReply($reply, $wordsFilter);
        return $result;
    }

    public function show(Reply $reply, ReplyBll $replyBll)
    {
        $reply = $replyBll->showReply($reply);

        return $reply;
    }

}