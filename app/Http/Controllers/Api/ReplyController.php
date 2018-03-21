<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:22
 */

namespace Hifone\Http\Controllers\Api;

use Auth;
use Hifone\Http\Bll\ReplyBll;
use Hifone\Models\Reply;
use Hifone\Services\Filter\WordsFilter;

class ReplyController extends ApiController
{
    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
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

        return $result;
    }
}