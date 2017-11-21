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

use Hifone\Events\Reply\ReplyWasTrashedEvent;
use Hifone\Events\Report\ReportWasPassedEvent;
use Hifone\Events\Thread\ThreadWasTrashedEvent;
use Hifone\Http\Controllers\Controller;
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
        $reports = Report::audited()->search($search)->with('user', 'lastOpUser', 'reportable')->orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.reports.index')
            ->withReports($reports)
            ->withCurrentMenu('index');
    }

    public function audit()
    {
        $reports = Report::audit()->orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.reports.audit')
            ->withReports($reports)
            ->withCurrentMenu('audit');
    }

    public function trash(Report $report)
    {
        $target = $report->reportable;
        $target->status = Thread::TRASH;
        if ($target instanceof Thread) {
            $operation = '删除帖子';
            $target->node->update(['thread_count' => $target->node->threads()->visible()->count()]);
            $target->user->update(['thread_count' => $target->user->threads()->visibleAndDeleted()->count()]);
            event(new ThreadWasTrashedEvent($target));

        } else {
            $operation = '删除回复';
            $target->user->decrement('reply_count', 1);
            event(new ReplyWasTrashedEvent($target));
        }
        $this->updateOpLog($target, $operation, trim(request('reason')));
        $reports = Report::where('reportable_id', $report->reportable_id)->where('reportable_type', $report->reportable_type)->get();
        foreach ($reports as $report) {
            $report->status = Report::DELETE;
            $this->updateOpLog($report, '处理举报', trim(request('reason')));
            event(new ReportWasPassedEvent($report));
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
