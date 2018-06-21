<?php
namespace Hifone\Console\Commands;

use DB;
use Carbon\Carbon;
use Hifone\Models\CreditRule;
use Hifone\Models\Node;
use Hifone\Models\Tag;
use Hifone\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;


class AddAutoTag extends Command
{
    protected $signature = 'add:autoTag';

    protected $description = 'add auto tags for users';

    public function __construct()
    {
        parent::__construct();
    }

    //根据用户信息，为用户自动打标签
    public function handle()
    {
        Redis::del('tag');
        $startDay = Carbon::today()->toDateTimeString();
        $previousTwoWeeks = Carbon::today()->subDay(14)->toDateTimeString();
        $previousFourWeeks = Carbon::today()->subDay(28)->toDateTimeString();
        $previousThirtyDays = Carbon::today()->subDay(30)->toDateTimeString();

        //取出所需的标签
        $newUserTag = Tag::where('name', '新用户')->first()->id;
        $recentLoginTag = Tag::where('name', '近期登陆')->first()->id;
        $twoWeeksActiveTag = Tag::where('name', '2周活跃高')->first()->id;
        $fourWeeksActiveTag = Tag::where('name', '4周活跃高')->first()->id;
        $contentTag = Tag::where('name', '内容贡献量多')->first()->id;
        $replyTag = Tag::where('name', '回帖质量高')->first()->id;
        $threadTag = Tag::where('name', '发帖质量高')->first()->id;
        $K2Tag= Tag::where('name', 'K2系列')->first()->id;
        $K3Tag= Tag::where('name', 'K3系列')->first()->id;
        $boxTag= Tag::where('name', '盒子')->first()->id;
        $R1Tag= Tag::where('name', 'R1')->first()->id;

        $loginRuleId =  CreditRule::where('slug', 'login')->first()->id;

        //取出所需的板块
        $k2_node_id =  Node::where('name', 'K2系列')->first()->id;
        $k3_node_id =  Node::where('name', 'K3系列')->first()->id;
        $box_node_id =  Node::where('name', '斐讯盒子')->first()->id;
        $ai_node_id =  Node::where('name', '斐讯AI音箱')->first()->id;


        //查询出注册时间在近两个星期的用户，打上新用户标签
        User::where('created_at', '>=',$previousTwoWeeks)->chunk(500, function ($users) use ($newUserTag) {
            foreach($users as $user) {
                Redis::hset('tag', $user->id, (string)($newUserTag));
            }
        });

        //近期登陆
        User::whereHas('credits' ,function($query) use ($previousTwoWeeks, $startDay, $recentLoginTag, $loginRuleId) {
            $query->where('created_at','>=', $previousTwoWeeks)->where('created_at','<', $startDay)->where('rule_id', $loginRuleId);
        })->chunk(500, function ($users) use ($recentLoginTag) {
            foreach($users as $user) {
                $userTag = $recentLoginTag;
                if(Redis::hGet('tag', $user->id)) {
                    $userTag = Redis::hGet('tag', $user->id) . ',' . $userTag;
                }
                Redis::hSet('tag', $user->id, (string)($userTag));
            }
        });


        //2周活跃
        $twoWeekLoginStat = DB::select("select count(credits.id) as login_count, credits.user_id as uid from credits 
                                        where credits.rule_id = ? 
                                        and credits.created_at >= ?
                                        and credits.created_at < ?
                                        group by uid
                                        having login_count >= 4", [$loginRuleId, $previousTwoWeeks, $startDay]);

        foreach ($twoWeekLoginStat as $twoStat) {
            $userTag = $twoWeeksActiveTag;
            if(Redis::hGet('tag', $twoStat->uid)) {
                $userTag = Redis::hGet('tag', $twoStat->uid) . ',' . $userTag;
            }
            Redis::hSet('tag', $twoStat->uid, (string)($userTag));
        }

        //4周活跃
        $fourWeekLoginStat = DB::select("select count(credits.id) as login_count, credits.user_id as uid from credits 
                                        where credits.rule_id = ? 
                                        and credits.created_at >= ?
                                        and credits.created_at < ?
                                        group by uid
                                        having login_count >= 6", [$loginRuleId, $previousFourWeeks, $startDay]);

        foreach ($fourWeekLoginStat as $fourStat) {
            $userTag = $fourWeeksActiveTag;
            if(Redis::hGet('tag', $fourStat->uid)) {
                $userTag = Redis::hGet('tag', $fourStat->uid) . ',' . $userTag;
            }
            Redis::hSet('tag', $fourStat->uid, (string)($userTag));
        }


        //内容贡献量多
        User::where('thread_count', '>', 0)->orWhere('reply_count', '>', 0)->chunk(500, function ($users) use ($contentTag){
            foreach ($users as $user) {
                if ($user->thread_count + $user->reply_count >= 10) {
                    $userTag = $contentTag;
                    if(Redis::hGet('tag', $user->id)) {
                        $userTag = Redis::hGet('tag', $user->id) . ',' . $userTag;
                    }
                    Redis::hSet('tag', $user->id, (string)($userTag));
                }
            }
        });

        //回帖质量高
        User::where('reply_count', '>', 0)->chunk(500, function ($users) use ($replyTag){
            foreach ($users as $user) {
                if ($user->replies()->visible()->where('order', 1)->count() + $user->replies()->visible()->where('order', '><',1)->where('like_count', '>=', 3)->count() > 10) {
                    $userTag = $replyTag;
                    if(Redis::hGet('tag', $user->id)) {
                        $userTag = Redis::hGet('tag', $user->id) . ',' . $userTag;
                    }
                    Redis::hSet('tag', $user->id, (string)($userTag));
                }
            }
        });

        //发帖质量高
        User::where('thread_count', '>', 0)->chunk(500, function ($users) use ($threadTag){
            foreach ($users as $user) {
                $statCount = 0;
                $threads = $user->threads()->visible()->where('is_excellent', '<>',1)->get();
                foreach ($threads as $thread) {
                    $thread['stats_count'] = $thread->like_count + $thread->reply_count + $thread->favorite_count;
                    if ($thread['stats_count'] >= 30) {
                        $statCount += 1;
                    }
                }
                if ($user->threads()->visible()->where('is_excellent', 1)->count() + $statCount >=2 ) {
                    $userTag = $threadTag;
                    if(Redis::hGet('tag', $user->id)) {
                        $userTag = Redis::hGet('tag', $user->id) . ',' . $userTag;
                    }
                    Redis::hSet('tag', $user->id, (string)($userTag));
                }
            }
        });

        $contentStat = DB::select("select count(threads.id) as thread_count, threads.user_id as uid from threads 
                                        where threads.status = 0
                                        and threads.created_at >= ? 
                                        and threads.created_at < ?
                                        group by uid
                                        having thread_count >= 10", [$loginRuleId, $previousThirtyDays, $startDay]);

        foreach ($contentStat as $stat) {
            if (User::find($stat->uid)->threads()->visible()->where('node_id', $k2_node_id)->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count() / $stat->thread_count >= 0.45 ) {
                $userTag = $K2Tag;
                if(Redis::hGet('tag', $stat->uid)) {
                    $userTag = Redis::hGet('tag', $stat->uid) . ',' . $userTag;
                }
                Redis::hSet('tag', $stat->uid, (string)($userTag));
            }

            if (User::find($stat->uid)->threads()->visible()->where('node_id', $k3_node_id)->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count() / $stat->thread_count >= 0.45 ) {
                $userTag = $K3Tag;
                if(Redis::hGet('tag', $stat->uid)) {
                    $userTag = Redis::hGet('tag', $stat->uid) . ',' . $userTag;
                }
                Redis::hSet('tag', $stat->uid, (string)($userTag));
            }

            if (User::find($stat->uid)->threads()->visible()->where('node_id', $box_node_id)->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count() / $stat->thread_count >= 0.45 ) {
                $userTag = $boxTag;
                if(Redis::hGet('tag', $stat->uid)) {
                    $userTag = Redis::hGet('tag', $stat->uid) . ',' . $userTag;
                }
                Redis::hSet('tag', $stat->uid, (string)($userTag));
            }

            if (User::find($stat->uid)->threads()->visible()->where('node_id', $ai_node_id)->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count() / $stat->thread_count >= 0.45 ) {
                $userTag = $R1Tag;
                if(Redis::hGet('tag', $stat->uid)) {
                    $userTag = Redis::hGet('tag', $stat->uid) . ',' . $userTag;
                }
                Redis::hSet('tag', $stat->uid, (string)($userTag));
            }
        }

        $it = NULL;
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $redis->select(7);
        while($keys = $redis->hScan('tag', $it)) {
            foreach ($keys as $key => $value) {
                $user = User::find($key);
                $tagData = explode(',', $value);
                $oldTagData =  $user->tags()->ofNotAuto()->get()->pluck('id')->toArray();
                $user->tags()->sync(array_merge($tagData, $oldTagData));
            }
        }

        \Log::debug('自动标签脚本运行完毕');
    }
}