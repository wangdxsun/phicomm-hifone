<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2017/10/20
 * Time: 16:49
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\QuestionBll;
use Hifone\Http\Bll\ReportBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Question;
use Hifone\Models\Reply;
use Hifone\Models\Thread;

class ReportController extends AppController
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

    public function question(Question $question, ReportBll $reportBll)
    {
        $reportBll->reportQuestion($question);

        return success('已提交');
    }

    public function answer(Answer $answer, ReportBll $reportBll)
    {
        $reportBll->reportAnswer($answer);

        return success('已提交');
    }

    public function comment(Comment $comment, ReportBll $reportBll)
    {
        $reportBll->reportComment($comment);

        return success('已提交');
    }

    public function reason(ReportBll $reportBll)
    {
        return $reportBll->getReason();
    }
}