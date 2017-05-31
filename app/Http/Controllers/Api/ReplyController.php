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

class ReplyController extends ApiController
{
    public function store(ReplyBll $replyBll)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new \Exception('您已被系统管理员禁言');
        }
        $replyBll->createReply();

        return success('回复发表成功，请耐心等待审核通过');
    }
}