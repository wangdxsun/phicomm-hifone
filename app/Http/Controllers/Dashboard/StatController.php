<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/6/14
 * Time: 14:02
 */

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Carousel;
use Hifone\Models\Node;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Input;
use DB;
use Carbon\Carbon;

class StatController extends Controller
{
    public function index()
    {
        return view('dashboard.stats.index')->withCurrentMenu('index');
    }

    public function banner()
    {
        $carousels = Carousel::recent()->paginate(10);
        return view('dashboard.stats.banner')->withCurrentMenu('banner')->withCarousels($carousels);
    }

    public function userCount()
    {
        $users = User::selectRaw('substr(created_at, 1, 10) as date, count(*) as cnt')->groupBy('date')->recent()->take(30)->get();
        $usersCount = User::count();
        return view('dashboard.stats.user')
            ->withCurrentMenu('user')
            ->with('users', $users)
            ->with('usersCount', $usersCount);
    }

    public function dailyThreadCount()
    {
        $dailyThreadCount = Thread::selectRaw('substr(created_at, 1, 10) as date, count(*) as total, sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->groupBy('date')->recent()->take(30)->get();
        $statsArr = array();
        foreach ($dailyThreadCount as $threadCount) {
            $statsArr[$threadCount['date']] = $threadCount->toArray();
        }

        return view('dashboard.stats.thread')
            ->withCurrentMenu('thread')
            ->with('statsArr', $statsArr);
    }

    public function dailyReplyCount()
    {
        $dailyReplyCount = Reply::selectRaw('substr(created_at, 1, 10) as date,count(*) as reply')
            ->visible()->groupBy('date')->recent()->take(30)->get();
        $statsArr = array();
        foreach ($dailyReplyCount as $replyCount) {
            $statsArr[$replyCount['date']] = $replyCount->toArray();
        }

        return view('dashboard.stats.reply')
            ->withCurrentMenu('reply')
            ->with('statsArr', $statsArr);
    }

    public function zeroReplyCount()
    {
        $dailyZeroThreadCount = Thread::selectRaw('substr(created_at, 1, 10) as date, count(*) as total, sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->where('reply_count', 0)->groupBy('date')->recent()->take(30)->get();

        $statsArr = array();
        foreach ($dailyZeroThreadCount as $threadCount) {
            $statsArr[$threadCount['date']] = $threadCount->toArray();
        }

        $allZeroReplyThreadCount = Thread::where('reply_count', 0)->count();

        return view('dashboard.stats.zeroReply')
            ->withCurrentMenu('zeroReply')
            ->with('allZeroReplyThreadCount', $allZeroReplyThreadCount)
            ->with('statsArr', $statsArr);
    }


    public function node()
    {
        $nodes = Node::orderBy('order')->get();
        return view('dashboard.stats.node')->withCurrentMenu('node')->withNodes($nodes);
    }

    public function node_detail(Node $node)
    {

        $search = $this->filterEmptyValue(Input::get('node'));
        $search['date_start'] = isset($search['date_start']) ? $search['date_start'] : substr(Thread::visible()->where('node_id',$node->id)->orderBy('id')->first()->created_at, 0,10);
        $search['date_end'] = isset($search['date_end']) ? $search['date_end'] :  substr(Thread::visible()->where('node_id',$node->id)->orderBy('id','desc')->first()->created_at, 0,10);
        $dailyThreadCount = DB::select("select stat.date, count(stat.tid) as thread_cnt, count(stat.rid) as reply_cnt from 
                                                (select t.id as tid, null as rid, substr(t.created_at, 1, 10) as date, t.node_id
                                                from threads as t
                                                where t.`status` = 0
                                                union all
                                                select null as tid, r.id as rid, substr(r.created_at, 1, 10) as date, t.node_id
                                                from replies as r
                                                join threads as t
                                                on r.thread_id = t.id
                                                where r.`status` = 0) as stat
                                                where stat.date >= ?
                                                and stat.date <= ?
                                                and stat.node_id = ?
                                                group by stat.date
                                                order by stat.date desc limit 30",
                                                [$search['date_start'], $search['date_end'],$node->id]);
        $statsArr = array();
        foreach ($dailyThreadCount as $threadCount) {
            $statsArr[$threadCount->date] = [
                'date' => $threadCount->date,
                'thread_count' => $threadCount->thread_cnt,
                'reply_count' => $threadCount->reply_cnt
            ];
        }
        $allThreadsCount = 0 ;
        $allRepliesCount = 0 ;

        foreach ($statsArr as $key => $value) {
            if ($key < $search['date_start'] && $key > $search['date_end']) {
                continue;
            } else {
                $allThreadsCount += $value['thread_count'];
                $allRepliesCount += $value['reply_count'];
            }
        }
        return view('dashboard.stats.node_detail')
            ->withCurrentMenu('node')
            ->with('statsArr', $statsArr)
            ->with('allThreadsCount', $allThreadsCount)
            ->with('allRepliesCount', $allRepliesCount);

    }

    public function banner_detail(Carousel $carousel)
    {
        $dailyStats = $carousel->dailyStats()->recent()->paginate(20);
        return view('dashboard.stats.banner_detail')->withCurrentMenu('banner')->withDailyStats($dailyStats);
    }
}