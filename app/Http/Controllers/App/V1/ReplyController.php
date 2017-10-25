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
use Hifone\Services\Filter\WordsFilter;
use Config;

class ReplyController extends AppController
{
    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        $reply = $replyBll->createReplyApp();
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