<?php
namespace Hifone\Console\Commands;

use Carbon\Carbon;
use Hifone\Models\CreditRule;
use Hifone\Models\Node;
use Hifone\Models\Tag;
use Hifone\Models\User;
use Illuminate\Console\Command;

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
        $tagData = [];
        $startDay = Carbon::today()->toDateTimeString();
        $previousTwoWeeks = Carbon::today()->subDay(14)->toDateTimeString();
        $previousFourWeeks = Carbon::today()->subDay(28)->toDateTimeString();
        $previousEightWeeks = Carbon::today()->subDay(56)->toDateTimeString();
        $previousThirtyDays = Carbon::today()->subDay(30)->toDateTimeString();
        //为注册时间在两周内的用户添加'新用户'标签
        User::with(['credits', 'threads', 'replies'])->chunk(100, function ($users) use ($tagData, $startDay, $previousTwoWeeks, $previousFourWeeks, $previousEightWeeks, $previousThirtyDays) {
            foreach ($users as $user) {
                //1.新用户
                $isRecentRegister = $user->credits()->where('rule_id', CreditRule::where('slug', 'register')->first()->id)->where('created_at','>=', $previousTwoWeeks)->where('created_at','<', $startDay)->count();
                if (0 != $isRecentRegister) {
                    array_push($tagData, Tag::where('name', '新用户')->first()->id);
                }

                //近期登录
                $isRecentLogin = $user->credits()->where('rule_id', CreditRule::where('slug', 'login')->first()->id)->where('created_at','>=', $previousTwoWeeks)->where('created_at','<', $startDay)->count();
                if (0 != $isRecentLogin) {
                    array_push($tagData, Tag::where('name', '近期登陆')->first()->id);
                }

                //2周活跃高
                if ($isRecentLogin >= 4) {
                    array_push($tagData, Tag::where('name', '2周活跃高')->first()->id);
                }

                //4周活跃高
                $isFourWeekActive = $user->credits()->where('rule_id', CreditRule::where('slug', 'login')->first()->id)->where('created_at','>=', $previousFourWeeks)->where('created_at','<', $startDay)->count();
                if ($isFourWeekActive >= 6) {
                    array_push($tagData, Tag::where('name', '4周活跃高')->first()->id);
                }

                //8周活跃高
                $isEightWeekActive = $user->credits()->where('rule_id', CreditRule::where('slug', 'login')->first()->id)->where('created_at','>=', $previousEightWeeks)->where('created_at','<', $startDay)->count();
                if ($isEightWeekActive >= 8) {
                    array_push($tagData, Tag::where('name', '8周活跃高')->first()->id);
                }

                //内容贡献量多
                if ($user->thread_count + $user->reply_count >= 10) {
                    array_push($tagData, Tag::where('name', '内容贡献量多')->first()->id);
                }

                //回帖质量高
                if ($user->replies()->visible()->where('order', 1)->count() + $user->replies()->visible()->where('order', '><',1)->where('like_count', '>=', 3)->count() > 10 ) {
                    array_push($tagData, Tag::where('name', '回帖质量高')->first()->id);
                }

                //发帖质量高
                $statCount = 0;
                $threads = $user->threads()->visible()->where('is_excellent', '<>',1)->get();
                foreach ($threads as $thread) {
                    $thread['stats_count'] = $thread->like_count + $thread->reply_count + $thread->favorite_count;
                    if ($thread['stats_count'] >= 30) {
                        $statCount += 1;
                    }
                }
                if ($user->threads()->visible()->where('is_excellent', 1)->count() + $statCount >=2 ) {
                    array_push($tagData, Tag::where('name', '发帖质量高')->first()->id);
                }

                $allCount = $user->threads()->visible()->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count();
                //产品
                if ($allCount >= 10) {
                    //K2系列
                    if ($user->threads()->visible()->where('node_id', Node::where('name', 'K2系列')->first()->id)->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count() / $allCount >= 0.45 ) {
                        array_push($tagData, Tag::where('name', 'K2系列')->first()->id);
                    }
                    if ($user->threads()->visible()->where('node_id', Node::where('name', 'K3系列')->first()->id)->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count() / $allCount >= 0.45) {
                        array_push($tagData, Tag::where('name', 'K3系列')->first()->id);
                    }
                    if ($user->threads()->visible()->where('node_id', Node::where('name', '斐讯盒子')->first()->id)->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count() / $allCount >= 0.45) {
                        array_push($tagData, Tag::where('name', '盒子')->first()->id);
                    }
                    if ($user->threads()->visible()->where('node_id', Node::where('name', '斐讯AI音箱')->first()->id)->where('created_at','>=', $previousThirtyDays)->where('created_at','<', $startDay)->count() / $allCount >= 0.45) {
                        array_push($tagData, Tag::where('name', 'R1')->first()->id);
                    }
                }
                $oldTagData =  $user->tags()->ofNotAuto()->get()->pluck('id')->toArray();
                $user->tags()->sync(array_merge($tagData, $oldTagData));
                $tagData = [];
            }
        });
        \Log::debug('自动标签脚本运行完毕');
    }
}