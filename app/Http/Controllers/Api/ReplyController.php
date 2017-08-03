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
        if (Config::get('settings.auto_audit',0) != 1) {
            return success('发表成功，待审核');
        }

        if ($replyBll->isContainsImageOrUrl($reply->body)) {
            return success('发表成功，待审核');
        } elseif ($wordsFilter->filterWord($reply->body)) {
            return success('发表成功，待审核');
        } else {
            $replyBll->replyPassAutoAudit($reply);
            return [
                'msg' => '发表成功！',
                'reply' => $reply
            ];
        }

    }
}