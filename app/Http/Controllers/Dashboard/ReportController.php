<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Events\Answer\AnswerWasDeletedEvent;
use Hifone\Events\Question\QuestionWasDeletedEvent;
use Hifone\Events\Reply\ReplyWasTrashedEvent;
use Hifone\Events\Report\ReportWasPassedEvent;
use Hifone\Events\Thread\ThreadWasTrashedEvent;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Answer;
use Hifone\Models\Comment;
use Hifone\Models\Question;
use Hifone\Models\Reply;
use Hifone\Models\Report;
use Hifone\Models\Thread;
use Redirect;
use View;
use Input;

class ReportController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share([
            'page_title'    => '举报管理',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('report'));
        $reports = Report::audited()->search($search)->threadAndReply()->with('user', 'lastOpUser', 'reportable')->orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.reports.index')
            ->withReports($reports)
            ->withCurrentMenu('index');
    }

    public function thread()
    {
        $reports = Report::audit()->threadAndReply()->with(['reportable', 'user'])->orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.reports.thread')
            ->withReports($reports)
            ->withCurrentMenu('thread');
    }

    public function question()
    {
        $reports = Report::audit()->questionAndAnswer()->with(['reportable', 'user'])->orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.reports.question')
            ->withReports($reports)
            ->withCurrentMenu('question');
    }

    public function trash(Report $report)
    {
        $target = $report->reportable;
        $target->status = Thread::DELETED;
        if ($target instanceof Thread) {
            $operation = '删除帖子';
            $target->node->update(['thread_count' => $target->node->threads()->visible()->count()]);
            event(new ThreadWasTrashedEvent($target));
        } elseif ($target instanceof Reply) {
            $operation = '删除评论';
            event(new ReplyWasTrashedEvent($target));
        } elseif ($target instanceof Question) {
            $operation = '删除提问';
            event(new QuestionWasDeletedEvent($target->user, $target));
        } elseif ($target instanceof Answer) {
            $operation = '删除回答';
            event(new AnswerWasDeletedEvent($target->user, $target));
        } elseif ($target instanceof Comment) {
            $operation = '删除回复';
        }
        $this->updateOpLog($target, $operation, trim(request('reason')));
        $reports = Report::where('reportable_id', $report->reportable_id)->where('reportable_type', $report->reportable_type)->get();
        $this->updateOpLog($report, '处理举报', trim(request('reason')));
        foreach ($reports as $report) {
            $report->status = Report::DELETE;
            $report->save();
            event(new ReportWasPassedEvent($report)); //给每个举报人加分
        }
        return Redirect::back()->withSuccess('删除成功');
    }

    public function ignore(Report $report)
    {
        $report->status = Report::IGNORE;
        $this->updateOpLog($report, '忽略举报');

        return Redirect::back()->withSuccess('忽略成功');
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return Redirect::back()->withSuccess('删除成功');
    }
}
