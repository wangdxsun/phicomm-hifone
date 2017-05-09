<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 21:02
 */

namespace Hifone\Http\Bll;

use Auth;
use Config;

class CreditBll extends BaseBll
{
    public function getCredits()
    {
        $credits = Auth::user()->credits()->with('rule')->recent()->paginate(Config::get('setting.per_page'));

        return $credits;
    }
}