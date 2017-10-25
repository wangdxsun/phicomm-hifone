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
        $badWord = '';
        if (Config::get('setting.auto_audit', 0) == 0  || ($badWord = $wordsFilter->filterWord($reply->body)) || $replyBll->isContainsImageOrUrl($reply->body)) {
            $reply->bad_word = $badWord;
            $msg = $replyBll->getMsg($reply->reply_id, false);
        } else {
            $replyBll->replyPassAutoAudit($reply);
            $msg = $replyBll->getMsg($reply->reply_id, true);
        }
        $reply->body = app('parser.at')->parse($reply->body);
        $reply->body = app('parser.emotion')->parse($reply->body);
        $reply->save();
        return [
            'msg' => $msg,
            'reply' => $reply
        ];
    }
}