<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 8:37
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\ReportBll;
use Hifone\Models\Reply;
use Hifone\Models\Thread;

class ReportController extends ApiController
{
    public function thread(Thread $thread, ReportBll $reportBll)
    {
        $reportBll->reportThread($thread);

        return success('已发送');
    }

    public function reply(Reply $reply, ReportBll $reportBll)
    {
        $reportBll->reportReply($reply);

        return success('已发送');
    }

    public function reason(ReportBll $reportBll)
    {
        return $reportBll->getReason();
    }
}