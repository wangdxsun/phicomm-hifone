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
use Auth;

class ReplyController extends AppController
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

    public function show(Reply $reply, ReplyBll $replyBll)
    {
        $reply = $replyBll->showReply($reply);

        return $reply;
    }

}