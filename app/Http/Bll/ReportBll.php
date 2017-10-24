<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 8:37
 */

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Models\Reply;
use Hifone\Models\Report;
use Hifone\Models\Thread;
use Input;

class ReportBll
{

    public function reportThread(Thread $thread)
    {
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();
        if (Auth::user()->hasReportThread($thread)) {
            throw new \Exception('你已经举报过了哦');
        }
        $thread->reports()->create($reportData);
    }

    public function reportReply(Reply $reply)
    {
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();
        if (Auth::user()->hasReportReply($reply)) {
            throw new \Exception('你已经举报过了哦');
        }
        $reply->reports()->create($reportData);
    }

    public function getReason()
    {
        return Report::$reason;
    }
}