<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/20
 * Time: 16:49
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\ReportBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Reply;
use Hifone\Models\Thread;

class ReportController extends AppController
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