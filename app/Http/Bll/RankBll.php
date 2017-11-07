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
        $lastMonday = Carbon::today()->previousWeekendDay()->subDay(6)->toDateTimeString();
        $ranks = Rank::where('start_date',$lastMonday)->orderBy('id')->get();
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
        $user->save();
    }


    public function rankCount()
    {
        return [
            '0' => User::where('rank_status',0)->count(),
            '1' => User::where('rank_status',1)->count(),
            '2' => User::where('rank_status',2)->count(),
            '3' => User::where('rank_status',3)->count(),
        ];
    }
}