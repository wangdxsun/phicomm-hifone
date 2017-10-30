<?php

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\RankBll;
use Hifone\Models\User;

class RankController extends ApiController
{
    public function ranks(RankBll $rankBll)
    {
        $ranks = $rankBll->ranks();
        return $ranks;
    }

    public function rankStatus(RankBll $rankBll)
    {
        $rankBll->rankStatus();
        return 'success';
    }

    public function rankCount(RankBll $rankBll)
    {
        return $rankBll->rankCount();
    }
}