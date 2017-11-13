<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:22
 */

namespace Hifone\Http\Controllers\Api;

use Config;
use Hifone\Http\Bll\ReplyBll;
use Hifone\Services\Filter\WordsFilter;

class ReplyController extends ApiController
{
    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        $reply = $replyBll->createReply();
        $result = $replyBll->auditReply($reply, $wordsFilter);

        return $result;
    }
}