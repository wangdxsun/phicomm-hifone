<?php

namespace Hifone\Http\Controllers\Api;

use Hifone\Models\Rank;

class RankController extends ApiController
{
    public function ranks()
    {
        $ranks = Rank::orderBy('id','desc')->limit(10)->get();
        return $ranks;
    }
}