<?php
namespace Hifone\Console\Commands;

use Carbon\Carbon;
use Hifone\Models\Reply;
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
        $lastSunday = Carbon::today()->previousWeekendDay()->addDay(1)->toDateTimeString();
        $threads = Thread::visible()->whereBetween('created_at',[$lastMonday,$lastSunday])
            ->with('user')->get()->groupBy('user_id');
        $replies = Reply::visible()->whereBetween('created_at',[$lastMonday,$lastSunday])->where('like_count', '>', 0)
            ->with('user')->get()->groupBy('user_id');

        $userRankCount = [];
        $week_rank = 0;
        //对于这一段时间的活跃用户，计算被点赞数，被评论数，被收藏数
        foreach ($threads as  $userId => $userThreads) {
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
            if (!User::find($userId)->can('manage_threads')) {
                $userRankCount[$userId] = [
                    'favoriteCount' => $favoriteCount,
                    'likeCount'     => $likeCount - $selfLikeCount,
                    'replyCount'    => $replyCount,
                    'all_count'     => $likeCount,
                    'user_id'       => $userId,
                    'score'         => User::find($userId)->score,
                    'week_rank'     => $week_rank,
                ];
            }
        }
        foreach ($replies as $userId => $userReplies) {
            $likeCount = 0;
            $replyCount = 0;
            $favoriteCount = 0;
            $selfLikeCount = 0;
            foreach ($userReplies as $userReply) {
                $likeCount += $userReply->like_count;
                $selfLikeCount += $userReply->selfLikeCount($userReply);
            }
            if (!User::find($userId)->can('manage_threads') && !array_key_exists($userId, $userRankCount)) {
                $userRankCount[$userId] = [
                    'favoriteCount' => $favoriteCount,
                    'likeCount'     => $likeCount - $selfLikeCount,
                    'replyCount'    => $replyCount,
                    'all_count'     => $likeCount,
                    'user_id'       => $userId,
                    'score'         => User::find($userId)->score,
                    'week_rank'     => $week_rank,
                ];
            } else {
                $userRankCount[$userId]['likeCount'] += $likeCount - $selfLikeCount;
            }
        }

        $userRankCount = collect($userRankCount)->sortByDesc(function ($user) {
            return $user['all_count'] * 100 + $user['score'] / 100;
        })->values()->all();
        $userRankCount = array_slice($userRankCount,0,10);
        Rank::where('start_date',$lastMonday)->delete();


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