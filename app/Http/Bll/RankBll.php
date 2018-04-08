<?php
namespace Hifone\Http\Bll;

use Carbon\Carbon;
use Hifone\Models\Rank;
use Hifone\Models\User;
use Auth;
use Input;

class RankBll extends BaseBll
{
    public function ranks()
    {
        $lastMonday = Carbon::today()->previous(Carbon::SUNDAY)->subDay(6)->toDateTimeString();
        $ranks = Rank::where('start_date',$lastMonday)->orderBy('id')->get()->load('user');
        foreach ($ranks as $rank) {
            $rank['followed'] = User::hasFollowUser(User::find($rank->user_id));
        }
        return $ranks;
    }

    public function rankStatus()
    {
        $rankData = Input::get('rank_status');
        $user =  Auth::user();
        $user->update(['rank_status' => $rankData]);
        if ($rankData == 1) {
            $msg = '加油加油~期待您下周榜上有名';
        } elseif ($rankData == 2) {
            $msg = '欢迎您下次光临哦~';
        } else {
            $msg = '受伤了~，不过我们会更加努力哒~';
        }
        return [
          'msg'  => $msg
        ];
    }

    public function count()
    {
        return [
            '未做选择' => User::where('rank_status',0)->count(),
            '期待上榜' => User::where('rank_status',1)->count(),
            '随便看看' => User::where('rank_status',2)->count(),
            '不感兴趣' => User::where('rank_status',3)->count(),
        ];
    }
}