<?php
namespace Hifone\Http\Bll;

use Hifone\Models\Rank;

class RankBll extends BaseBll
{
    public function ranks()
    {
        $ranks = Rank::orderBy('id','desc')->limit(10)->get()->load('user');
        return $ranks;
    }
}