<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\RankBll;

class RankController extends WebController
{
    public function ranks(RankBll $rankBll)
    {
        $ranks = $rankBll->ranks();
        return $ranks;
    }

    public function rankStatus(RankBll $rankBll)
    {
        return $rankBll->rankStatus();
    }

    public function count(RankBll $rankBll)
    {
        return $rankBll->count();
    }
}