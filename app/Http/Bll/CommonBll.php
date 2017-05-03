<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 20:06
 */

namespace Hifone\Http\Bll;

use Auth;
use Hifone\Events\User\UserWasLoggedinEvent;

class CommonBll extends BaseBll
{
    public function checkLogin()
    {
        if (Auth::check()) {
            $activeDate = app('session')->get('active_date');

            if (!$activeDate || $activeDate != date('Ymd')) {
                event(new UserWasLoggedinEvent(Auth::user()));
                app('session')->put('active_date', date('Ymd'));
            }
        }
    }
}