<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\ReportBll;
use Hifone\Models\Reply;
use Hifone\Models\Thread;

class ReportController extends WebController
{
    public function thread(Thread $thread, ReportBll $reportBll)
    {
        $reportBll->reportThread($thread);

        return success('已提交');
    }

    public function reply(Reply $reply, ReportBll $reportBll)
    {
        $reportBll->reportReply($reply);

        return success('已提交');
    }

    public function reason(ReportBll $reportBll)
    {
        return $reportBll->getReason();
    }
}