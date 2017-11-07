<?php
namespace Hifone\Http\Bll;

use Hifone\Models\Rank;
use Hifone\Models\User;
use Auth;
use Input;

class RankBll extends BaseBll
{
    public function ranks()
    {
        $ranks = Rank::orderBy('id','desc')->limit(10)->get()->load('user');
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