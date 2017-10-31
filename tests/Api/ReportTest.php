<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/10/30
 * Time: 9:27
 */

namespace Hifone\Test\Api;

use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;

class ReportTest extends ApiTestCase
{
    public function testReportThread()
    {
        $thread = Thread::visible()->first();
        $reported = $this->user->hasReportThread($thread);
        $this->post('/report/threads/'.$thread->id, ['report' => ['reason' => '单元测试']]);
        if ($reported) {
            $this->seeJson(['msg' => '你已经举报过了哦']);
        } else {
            $this->seeJson(['msg' => '举报成功']);
        }
    }

    public function testReportReply()
    {
        $reply = Reply::visible()->first();
        $reported = $this->user->hasReportReply($reply);
        $this->post('/report/replies/'.$reply->id, ['report' => ['reason' => '单元测试']]);
        if ($reported) {
            $this->seeJson(['msg' => '你已经举报过了哦']);
        } else {
            $this->seeJson(['msg' => '举报成功']);
        }
    }
}