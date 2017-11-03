<?php
namespace Hifone\Console\Commands;

use Carbon\Carbon;
use Hifone\Models\Thread;
use Hifone\Models\User;
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
            foreach ($userThreads as $userThread) {
                $favoriteCount += $userThread->favorite_count;
                $likeCount += $userThread->like_count;
                $replyCount += $userThread->reply_count;
                $selfReplyCount += $userThread->selfReplyCount($userThread);
                $selfLikeCount += $userThread->selfLikeCount($userThread);
                $selfFavoriteCount += $userThread->selfFavoriteCount($userThread);
            }
            array_push($userRankCount, [
                'favoriteCount' => $favoriteCount - $selfFavoriteCount,
                'likeCount'     => $likeCount - $selfLikeCount,
                'replyCount'    => $replyCount - $selfReplyCount,
                'all_count'     => User::find($user_id)->can('manage_threads') ? -1 : ($replyCount - $selfReplyCount
                                   + $favoriteCount - $selfFavoriteCount + $likeCount - $selfLikeCount),
                'user_id'       => $user_id,
                'score'         => User::find($user_id)->can('manage_threads') ? -1 : User::find($user_id)->score,
                'week_rank'     => $week_rank,
            ]);
        }
        $userRankCount = collect($userRankCount)->sortByDesc('score')->sortByDesc('all_count')->values()->all();
        $userRankCount = array_slice($userRankCount,0,10);


        foreach ($userRankCount as $data) {
            Rank::create([
                'favorite_count' => $data['favoriteCount'],
                'like_count'     => $data['likeCount'],
                'reply_count'    => $data['replyCount'],
                'user_id'        => $data['user_id'],
                'start_date'     => $lastMonday,
                'end_date'       => $lastSunday,
                'week_rank'      => ++$week_rank,
                'score'          => $data['score'],
            ]);
        }
    }
}