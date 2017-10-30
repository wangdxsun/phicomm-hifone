<?php

namespace Hifone\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Rank;
use Hifone\Models\Thread;
use Hifone\Models\User;

class CheckController extends  Controller
{
    public function check() {
        $lastMonday = Carbon::today()->previousWeekendDay()->subDay(566)->toDateTimeString();
        $lastSunday = Carbon::today()->previousWeekendDay()->addDays(1)->toDateTimeString();
        $threads = Thread::visible()->whereBetween('created_at',[$lastMonday,$lastSunday])
            ->with('user')->get()->groupBy('user_id');
        $userRankCount = [];
        $week_rank = 0;
        //对于这一段时间的活跃用户，计算被点赞数，被评论数，被收藏数
        foreach ($threads as  $user_id => $userThreads) {
            $favoriteCount = 0;
            $likeCount = 0;
            $replyCount = 0;
            $selfReplyCount = 0;
            $selfLikeCount = 0;
            $selfFavoriteCount = 0;
            $week_rank += 1;
            foreach ($userThreads as $userThread) {
                $favoriteCount += $userThread->favorite_count;
                $likeCount += $userThread->like_count;
                $replyCount += $userThread->reply_count;
                $selfReplyCount += $userThread->selfReplyCount($userThread);
                $selfLikeCount += $userThread->selfLikeCount($userThread);
                $selfFavoriteCount += $userThread->selfFavoriteCount($userThread);
            }
            array_push($userRankCount,[
                'favoriteCount' => $favoriteCount - $selfFavoriteCount,
                'likeCount' => $likeCount - $selfLikeCount,
                'replyCount' => $replyCount - $selfReplyCount,
                'all_count' => $replyCount - $selfReplyCount + $favoriteCount - $selfFavoriteCount + $likeCount - $selfLikeCount,
                'user_id' => $user_id,
                'score' => User::find($user_id)->score,
                'followed' => User::hasFollowUser(User::find($user_id)),
                'week_rank' => $week_rank]);
        }
        collect($userRankCount)->sortByDesc('all_count')->sortByDesc('score')->toArray();
        $userRankCount = array_slice($userRankCount,0,10);
        foreach ($userRankCount as $data) {
            Rank::create(['favoriteCount' => $data['favoriteCount'],
            'likeCount'     => $data['likeCount'],
            'replyCount'    => $data['replyCount'],
            'user_id'       => $data['user_id'],
            'start_date'    => $lastMonday,
            'end_date'      => $lastSunday,
            'week_rank'     => $data['week_rank'],
            'score'         => $data['score'],
            'followed'      => $data['followed']
            ]);
        }
    }
}
