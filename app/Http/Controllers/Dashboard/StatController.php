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
    //数据统计之用户统计基本情况
    public function userCount()
    {
        $userStat = DB::select('select stat.date, count(DISTINCT stat.tid) as thread_user_cnt, count(DISTINCT stat.rid) as reply_user_cnt,count(DISTINCT all_id) as  contribute_user_cnt,count(DISTINCT uid) as user_cnt from 
                                        (select t.user_id as tid, null as rid, t.user_id as all_id, null as uid, substr(t.created_at, 1, 10) as date
                                        from threads as t
                                        where t.`status` = 0
                                        union all
                                        select null as tid, r.user_id as rid, r.user_id as all_id, null as uid,substr(r.created_at, 1, 10) as date
                                        from replies as r
                                        where r.`status` = 0
                                        union ALL
                                        select null as tid, null as rid, null as all_id, u.id as uid,substr(u.created_at, 1, 10) as date
                                        from users as u
                                        ) as stat
                                        group by stat.date
                                        order by stat.date desc limit 30');
        $statsArr = array();
        foreach ($userStat as $userCount) {
            $statsArr[$userCount->date] = [
                'date' => $userCount->date,
                'user_count' => $userCount->user_cnt,
                'thread_user_count' => $userCount->thread_user_cnt,
                'reply_user_count' => $userCount->reply_user_cnt,
                'contribute_user_count' => $userCount->contribute_user_cnt,
            ];
        }

        return view('dashboard.stats.user')
            ->withCurrentMenu('user')
            ->with('statArr', $statsArr)
            ->with('src', 'basic')
            ->withCurrentTap('basic');
    }
    //用户统计之App活跃用户
    public function userCountApp()
    {

    }
    //用户统计之Web活跃用户
    public function userCountWeb()
    {

    }
    //用户统计之H5活跃用户
    public function userCountH5()
    {

    }

    //用户互动
    public function userInteraction()
    {
        $userStat = DB::select("select stat.date, 
                                        count(DISTINCT stat.favorite_id) as favorite_cnt,
                                        count(DISTINCT stat.like_id) as like_cnt,
                                        count(DISTINCT stat.follow_id) as  follow_cnt from 
                                        (select substr(f.created_at,1,10) as date, id as favorite_id,null as like_id,null as follow_id
                                        from favorites as f 
                                        union all  
                                        select substr(l.created_at,1,10) as date,null as favorite_id,id as like_id,null as follow_id
                                        from likes as l
                                        union all 
                                        select substr(ff.created_at,1,10) as date,null as favorite_id,null as like_id,id as follow_id
                                        from follows as ff 
                                        where ff.`followable_type`= ?) as stat
                                        group by stat.date
                                        order by stat.date desc limit 30",['Hifone\Models\User']);
        $statsArr = array();
        foreach ($userStat as $userCount) {
            $statsArr[$userCount->date] = [
                'date' => $userCount->date,
                'favorite_count' => $userCount->favorite_cnt,
                'like_count' => $userCount->like_cnt,
                'follow_count' => $userCount->follow_cnt,
            ];
        }
        return view('dashboard.stats.user_interaction')
            ->with('statArr', $statsArr)
            ->withCurrentMenu('userInteraction');
    }
    //数据统计之每日新增发帖统计
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
    //数据统计之新增发帖统计
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
    //数据统计之零回复统计
    public function zeroReplyCount()
    {
        $dailyZeroThreadCount = Thread::selectRaw('substr(created_at, 1, 10) as date, count(*) as total, sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->where('reply_count', 0)->groupBy('date')->recent()->take(30)->get();

        $statsArr = array();
        $recentZeroReplyThreadCount = 0;
        foreach ($dailyZeroThreadCount as $threadCount) {
            $statsArr[$threadCount['date']] = $threadCount->toArray();
            $recentZeroReplyThreadCount += $threadCount['total'];
        }

        $allZeroReplyThreadCount = Thread::where('reply_count', 0)->count();

        return view('dashboard.stats.zeroReply')
            ->withCurrentMenu('zeroReply')
            ->with('allZeroReplyThreadCount', $allZeroReplyThreadCount)
            ->with('recentZeroReplyThreadCount', $recentZeroReplyThreadCount)
            ->with('statsArr', $statsArr);
    }

   //数据统计之板块统计
    public function node()
    {
        $nodes = Node::orderBy('order')->get();
        return view('dashboard.stats.node')->withCurrentMenu('node')->withNodes($nodes);
    }
    //板块统计之详情
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
    //banner统计之详情
    public function banner_detail(Carousel $carousel)
    {
        $dailyStats = $carousel->dailyStats()->recent()->paginate(20);
        return view('dashboard.stats.banner_detail')->withCurrentMenu('banner')->withDailyStats($dailyStats);
    }
}