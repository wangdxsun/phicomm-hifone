<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Rank;


class CheckController extends  Controller
{
    public function check() {
        Rank::create([
            'favoriteCount' => 1,
            'like_count'     => 1,
            'replyCount'    => 1,
            'user_id'       => 1,
            'start_date'    => 1,
            'end_date'      => 1,
            'week_rank'     => 1,
            'score'         => 1,
            'followed'      => 1
        ]);
        return 'created';
    }
}
