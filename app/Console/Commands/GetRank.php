<?php
namespace Hifone\Console\Commands;

use Carbon\Carbon;
use Hifone\Models\Favorite;
use Hifone\Models\Like;
use Hifone\Models\Reply;
use Illuminate\Console\Command;
use Hifone\Models\Rank;

class GetRank extends Command
{
    protected $signature = 'get:rank';

    protected $description = 'get excellent users rank';

    public function __construct()
    {
        parent::__construct();
    }

    //根据被点赞数，被评论数和被收藏数筛选优质用户
    public function handle()
    {
        $lastMonday = Carbon::today()->previousWeekendDay()->subDay(6)->toDateTimeString();
        $lastSunday = Carbon::today()->previousWeekendDay()->addDay()->subSecond()->toDateTimeString();
        $userRankCount = [];
        $week_rank = 0;
        //处理点赞逻辑，首先拿到上个星期被点赞了的帖子和回复，以及对应的用户信息
        $likes = Like::whereBetween('created_at',[$lastMonday,$lastSunday])->with(['likeable' => function($query) {
            $query->where('status', 0);
        }])->get();
        foreach ($likes as $like) {
            if ( null != $like->likeable && $like->user_id !== $like->likeable->user_id && !$like->likeable->user->can('manage_threads')) {
                if (isset($userRankCount[$like->likeable->user_id])) {
                    $userRankCount[$like->likeable->user_id]['like'] += 1;
                } else {
                    $userRankCount[$like->likeable->user_id]['user_id'] = $like->likeable->user_id;
                    $userRankCount[$like->likeable->user_id]['like'] = 1;
                    $userRankCount[$like->likeable->user_id]['favorite'] = 0;
                    $userRankCount[$like->likeable->user_id]['reply'] = 0;
                    $userRankCount[$like->likeable->user_id]['score'] = $like->likeable->user->score;
                }
            }
        }
        $replies = Reply::whereBetween('created_at',[$lastMonday,$lastSunday])->visible()->with([
            'thread' => function ($query) {
                $query->where('status', 0);
            }, 'thread.user'])->get();
        foreach ($replies as $reply) {
            if (null != $reply->thread && $reply->user_id !== $reply->thread->user_id && !$reply->thread->user->can('manage_threads')) {
                if (isset($userRankCount[$reply->thread->user_id])) {
                    $userRankCount[$reply->thread->user_id]['reply'] += 1;
                } else {
                    $userRankCount[$reply->thread->user_id]['user_id'] = $reply->thread->user_id;
                    $userRankCount[$reply->thread->user_id]['reply'] = 1;
                    $userRankCount[$reply->thread->user_id]['like'] = 0;
                    $userRankCount[$reply->thread->user_id]['favorite'] = 0;
                    $userRankCount[$reply->thread->user_id]['score'] = $reply->thread->user->score;
                }
            }
        }
        $favorites = Favorite::whereBetween('created_at',[$lastMonday,$lastSunday])->with([
            'thread' => function ($query) {
                $query->where('status', 0);
            }, 'thread.user'])->get();

        foreach ($favorites as $favorite) {
            if (null != $favorite->thread && $favorite->user_id !== $favorite->thread->user_id && !$favorite->thread->user->can('manage_threads')) {
                if (isset($userRankCount[$favorite->thread->user_id])) {
                    $userRankCount[$favorite->thread->user_id]['favorite'] += 1;
                } else {
                    $userRankCount[$favorite->thread->user_id]['user_id'] = $favorite->thread->user_id;
                    $userRankCount[$favorite->thread->user_id]['favorite'] = 1;
                    $userRankCount[$favorite->thread->user_id]['like'] = 0;
                    $userRankCount[$favorite->thread->user_id]['reply'] = 0;
                    $userRankCount[$favorite->thread->user_id]['score'] = $favorite->thread->user->score;
                }
            }
        }

        $userRankCount = collect($userRankCount)->sortByDesc(function ($user) {
            return ($user['like'] + $user['favorite'] +$user['reply']) * 100 + $user['score'] / 100;
        })->values()->all();
        $userRankCount = array_slice($userRankCount,0,10);
        Rank::where('start_date',$lastMonday)->delete();

        foreach ($userRankCount as $data) {
            Rank::create([
                'favorite_count' => $data['favorite'],
                'like_count'     => $data['like'],
                'reply_count'    => $data['reply'],
                'user_id'        => $data['user_id'],
                'start_date'     => $lastMonday,
                'end_date'       => $lastSunday,
                'week_rank'      => ++$week_rank,
                'score'          => $data['score'],
            ]);
        }
    }
}