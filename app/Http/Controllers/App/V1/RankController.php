<?php

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\RankBll;
use Hifone\Http\Controllers\App\AppController;

class RankController extends AppController
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