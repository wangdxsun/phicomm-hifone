<?php

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\RankBll;

class RankController extends ApiController
{
    public function ranks(RankBll $rankBll)
    {
        $ranks = $rankBll->ranks();
        return $ranks;
    }
}