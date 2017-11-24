<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 8:37
 */

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Reply;
use Hifone\Models\Report;
use Hifone\Models\Thread;
use Input;

class ReportBll
{

    public function reportThread(Thread $thread)
    {
        if (Auth::id() === $thread->user->id) {
            throw new HifoneException('自己不能举报自己哦');
        }
        if (Auth::user()->hasReportThread($thread)) {
            throw new HifoneException('你已经举报过了哦');
        }
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();

        $thread->reports()->create($reportData);
    }

    public function reportReply(Reply $reply)
    {
        if (Auth::id() === $reply->user->id) {
            throw new HifoneException('自己不能举报自己哦');
        }
        if (Auth::user()->hasReportReply($reply)) {
            throw new HifoneException('你已经举报过了哦');
        }
        $reportData = Input::get('report');
        $reportData['user_id'] = Auth::id();

        $reply->reports()->create($reportData);
    }

    public function getReason()
    {
        return Report::$reason;
    }
}