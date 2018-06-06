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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Input;
use DB;

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
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;

        $dateStat = DB::select("select count(stat.date) as date_cnt from 
                                        (select substr(t.created_at, 1, 10) as date
                                        from threads as t
                                        where t.`status` = 0
                                        union 
                                        select substr(r.created_at, 1, 10) as date
                                        from replies as r
                                        where r.`status` = 0
                                        union 
                                        select substr(u.created_at, 1, 10) as date
                                        from users as u
                                        ) as stat");
        $total = array_get($dateStat,0)->date_cnt;

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
                                        order by stat.date desc limit 30 offset ?', [$skip]);

        $stats = new LengthAwarePaginator($userStat, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

        return view('dashboard.stats.user')
            ->withCurrentMenu('user')
            ->with('stats', $stats)
            ->withCurrentNav('basic');
    }
    //用户统计之App活跃用户
    public function userCountApp()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;

        $dateStat = DB::select("select count(stat.date) as date_cnt from (
                                        select substr(u.last_active_time_app,1,10) as date
                                        from users as u 
                                        where u.last_active_time_app is not null
                                        union  
                                        select substr(u.last_visit_time_app,1,10) as date
                                        from users as u 
                                        where substr(u.last_visit_time_app,1,10) = SUBSTR(u.created_at,1,10)
                                        union 
                                        select substr(u.last_visit_time_app,1,10) as date
                                        from users as u 
                                        where substr(u.last_visit_time_app,1,10) > SUBSTR(u.created_at,1,10)) as stat");
        $total = array_get($dateStat,0)->date_cnt;
        $userStat = DB::select('select stat.date,
                                        count(DISTINCT uid) as active_user_cnt, 
                                        count(DISTINCT new_id) as new_user_cnt,
                                        count(DISTINCT old_id) as  old_user_cnt
                                        from(
                                        select substr(u.last_active_time_app,1,10) as date, u.id as uid,null as new_id,null as old_id 
                                        from users as u 
                                        where u.last_active_time_app is not null
                                        union all 
                                        select substr(u.last_visit_time_app,1,10) as date,null as uid, u.id as new_id,null as old_id
                                        from users as u 
                                        where substr(u.last_visit_time_app,1,10) = SUBSTR(u.created_at,1,10)
                                        union ALL
                                        select substr(u.last_visit_time_app,1,10) as date,null as uid,null as new_id,u.id as old_id
                                        from users as u 
                                        where substr(u.last_visit_time_app,1,10) > SUBSTR(u.created_at,1,10)) as stat
                                        group by stat.date
                                        order by stat.date desc limit 30 offset ?', [$skip]);
        $stats = new LengthAwarePaginator($userStat, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);
        return view('dashboard.stats.user_active')
            ->with('stats', $stats)
            ->withCurrentNav('app')
            ->withCurrentMenu('user');
    }
    //用户统计之Web活跃用户
    public function userCountWeb()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;

        $dateStat = DB::select("select count(stat.date) as date_cnt from (
                                        select substr(u.last_active_time_app,1,10) as date
                                        from users as u 
                                        where u.last_visit_time_web is not null
                                        union  
                                        select substr(u.last_visit_time_web,1,10) as date
                                        from users as u 
                                        where substr(u.last_visit_time_web,1,10) = SUBSTR(u.created_at,1,10)
                                        union 
                                        select substr(u.last_visit_time_web,1,10) as date
                                        from users as u 
                                        where substr(u.last_visit_time_web,1,10) > SUBSTR(u.created_at,1,10)) as stat");
        $total = array_get($dateStat,0)->date_cnt;
        $userStat = DB::select('select stat.date,
                                        count(DISTINCT uid) as active_user_cnt, 
                                        count(DISTINCT new_id) as new_user_cnt,
                                        count(DISTINCT old_id) as  old_user_cnt
                                        from(
                                        select substr(u.last_active_time_web,1,10) as date, u.id as uid,null as new_id,null as old_id 
                                        from users as u 
                                        where u.last_active_time_web is not null
                                        union all 
                                        select substr(u.last_visit_time_web,1,10) as date,null as uid, u.id as new_id,null as old_id
                                        from users as u 
                                        where substr(u.last_visit_time_web,1,10) = SUBSTR(u.created_at,1,10)
                                        union ALL
                                        select substr(u.last_visit_time_web,1,10) as date,null as uid,null as new_id,u.id as old_id
                                        from users as u 
                                        where substr(u.last_visit_time_web,1,10) > SUBSTR(u.created_at,1,10)) as stat
                                        group by stat.date
                                        order by stat.date desc limit 30 offset ?', [$skip]);

        $stats = new LengthAwarePaginator($userStat, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

        return view('dashboard.stats.user_active')
            ->with('stats', $stats)
            ->withCurrentNav('web')
            ->withCurrentMenu('user');
    }
    //用户统计之H5活跃用户
    public function userCountH5()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;

        $dateStat = DB::select("select count(stat.date) as date_cnt from (
                                        select substr(u.last_active_time,1,10) as date
                                        from users as u 
                                        where u.last_active_time is not null
                                        union  
                                        select substr(u.last_active_time,1,10) as date
                                        from users as u 
                                        where substr(u.last_active_time,1,10) = SUBSTR(u.created_at,1,10)
                                        union 
                                        select substr(u.last_active_time,1,10) as date
                                        from users as u 
                                        where substr(u.last_visit_time_web,1,10) > SUBSTR(u.created_at,1,10)) as stat");
        $total = array_get($dateStat,0)->date_cnt;
        $userStat = DB::select('select stat.date,
                                        count(DISTINCT uid) as active_user_cnt, 
                                        count(DISTINCT new_id) as new_user_cnt,
                                        count(DISTINCT old_id) as  old_user_cnt
                                        from(
                                        select substr(u.last_active_time,1,10) as date, u.id as uid,null as new_id,null as old_id 
                                        from users as u 
                                        where u.last_active_time is not null
                                        union all 
                                        select substr(u.last_visit_time,1,10) as date,null as uid, u.id as new_id,null as old_id
                                        from users as u 
                                        where substr(u.last_visit_time,1,10) = SUBSTR(u.created_at,1,10)
                                        union ALL
                                        select substr(u.last_visit_time,1,10) as date,null as uid,null as new_id,u.id as old_id
                                        from users as u 
                                        where substr(u.last_visit_time,1,10) > SUBSTR(u.created_at,1,10)) as stat
                                        group by stat.date
                                        order by stat.date desc limit 30 offset ?', [$skip]);
        $stats = new LengthAwarePaginator($userStat, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);
        return view('dashboard.stats.user_active')
            ->with('stats', $stats)
            ->withCurrentNav('h5')
            ->withCurrentMenu('user');

    }

    //用户互动
    public function userInteraction()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;
        $dateStat = DB::select("select count(stat.date) as date_cnt from 
                                            (select substr(f.created_at,1,10) as date
                                            from favorites as f 
                                            union  
                                            select substr(l.created_at,1,10) as date
                                            from likes as l
                                            union  
                                            select substr(ff.created_at,1,10) as date
                                            from follows as ff 
                                            where ff.`followable_type`= ?) as stat",['Hifone\Models\User']);
        $total = array_get($dateStat,0)->date_cnt;

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
                                        order by stat.date desc limit 30 offset ?",['Hifone\Models\User', $skip]);
        $stats = new LengthAwarePaginator($userStat, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);
        return view('dashboard.stats.user_interaction')
            ->with('stats', $stats)
            ->withCurrentMenu('userInteraction');
    }
    //新增发帖
    public function dailyThreadCount()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;
        $threadCount = Thread::selectRaw('substr(created_at, 1, 10) as date, count(*) as total, sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->groupBy('date')->recent()->get();
        $total = count($threadCount);
        $dailyThreadCount = Thread::selectRaw('substr(created_at, 1, 10) as date, count(*) as total, sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->groupBy('date')->recent()->skip($skip)->take(30)->get();
        $stats = new LengthAwarePaginator($dailyThreadCount, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

        return view('dashboard.stats.thread')
            ->withCurrentMenu('thread')
            ->with('stats', $stats);
    }
    //新增回帖
    public function dailyReplyCount()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;
        $replyCount = Reply::selectRaw('substr(created_at, 1, 10) as date,count(*) as reply,  sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->groupBy('date')->recent()->get();
        $total = count($replyCount);
        $dailyReplyCount = Reply::selectRaw('substr(created_at, 1, 10) as date,count(*) as reply,  sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->groupBy('date')->recent()->skip($skip)->take(30)->get();

        $stats = new LengthAwarePaginator($dailyReplyCount, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

        return view('dashboard.stats.reply')
            ->withCurrentMenu('reply')
            ->with('stats', $stats);
    }

    //新增回答和提问
    public function dailyQuestionsAndAnswersCount()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;
        $dateStat = DB::select("select count(stat.date) as date_cnt from 
                                                (select substr(q.created_at, 1, 10) as date
                                                from questions as q
                                                where q.`status` = 0
                                                union 
                                                select substr(a.created_at, 1, 10) as date
                                                from answers as a
                                                join questions as q
                                                on a.question_id = q.id
                                                where a.`status` = 0) as stat 
                                            ");
        $total = array_get($dateStat,0)->date_cnt;

        $dailyCount = DB::select("select stat.date, count(stat.qid) as question_cnt, count(stat.aid) as answer_cnt, count(stat.cid) as comment_cnt from 
                                                (select q.id as qid, null as aid, null as cid, substr(q.created_at, 1, 10) as date
                                                from questions as q
                                                where q.`status` = 0
                                                union all
                                                select null as qid, a.id as aid, null as cid, substr(a.created_at, 1, 10) as date
                                                from answers as a
                                                join questions as q
                                                on a.question_id = q.id
                                                where a.`status` = 0
                                                union all
                                                select null as qid, null as aid, c.id as cid, substr(c.created_at, 1, 10) as date
                                                from comments as c
                                                join answers as a
                                                on c.answer_id = a.id
                                                where c.`status` = 0
                                                ) as stat
                                                group by stat.date
                                                order by stat.date desc limit 30 offset  ? ", [$skip]);

        $stats = new LengthAwarePaginator($dailyCount, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);
        $questionsCount = 0;
        $answersCount = 0;
        $commentsCount = 0;
        foreach (array_get($stats->toArray(), 'data') as  $value) {
            $questionsCount += $value->question_cnt;
            $answersCount += $value->answer_cnt;
            $commentsCount += $value->comment_cnt;
        }
        return view('dashboard.stats.questions')
            ->with('stats', $stats)
            ->with('questionsCount', $questionsCount)
            ->with('answersCount', $answersCount)
            ->with('commentsCount', $commentsCount)
            ->withCurrentMenu('question_answer');
    }

    //零回复统计
    public function zeroReplyCount()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;
        $zeroThreadCount = Thread::selectRaw('substr(created_at, 1, 10) as date, count(*) as total, sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->where('reply_count', 0)->groupBy('date')->recent()->get();
        $total = count($zeroThreadCount) > 360 ? 360 : count($zeroThreadCount);

        $dailyZeroThreadCount = Thread::selectRaw('substr(created_at, 1, 10) as date, count(*) as total, sum(abs(channel)) as feedback,sum(if(channel = 0, 1, 0)) as forum')
            ->visible()->where('reply_count', 0)->groupBy('date')->recent()->skip($skip)->take(30)->get();

        $stats = new LengthAwarePaginator($dailyZeroThreadCount, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

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
            ->with('stats', $stats);
    }

   //数据统计之版块统计
    public function node()
    {
        $nodes = Node::orderBy('order')->get();
        return view('dashboard.stats.node')->withCurrentMenu('node')->withNodes($nodes);
    }
    //版块统计之详情
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

    //用户搜索词统计
    public function userSearch()
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;
        $dateStat = DB::select("select count(DISTINCT substr(created_at,1,10)) as date_cnt from search_words ");
        $total = array_get($dateStat,0)->date_cnt;

        $userStat = DB::select("select substr(created_at,1,10) as date,sum(`count`) as cnt
                                        from search_words
                                        group by date
                                        order by date desc limit 30 offset ?", [$skip]);

        $stats = new LengthAwarePaginator($userStat, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

        return view('dashboard.stats.search_words')
            ->with('stats', $stats)
            ->withCurrentMenu('search_words');

    }

    //搜索词详情
    public function userSearchDate($date)
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $skip = ($currentPage - 1) * 30;
        $dateStat = DB::select("select count(word) as stat_cnt
                                        from search_words
                                        where substr(created_at,1,10) = ? ", [$date]);
        $total = array_get($dateStat,0)->stat_cnt;

        $dailyStat = DB::select("select word,`count` as daily_cnt,`stat_count` as stat_cnt
                                        from search_words
                                        where substr(created_at,1,10) = ?
                                        order by daily_cnt desc limit 30 offset ?",[$date, $skip]);
        $stats = new LengthAwarePaginator($dailyStat, $total, 30, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

        return view('dashboard.stats.search_detail')
            ->with('stats', $stats)
            ->withCurrentMenu('search_detail');
    }

}