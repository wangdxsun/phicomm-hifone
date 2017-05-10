<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/9
 * Time: 8:47
 */

namespace Hifone\Http\Bll;

use Auth;
use Config;
use Hifone\Models\User;

class UserBll extends BaseBll
{
    public function allUser()
    {
//        $users = User::;
    }

    public function getCredits()
    {
        $credits = Auth::user()->credits()->with('rule')->recent()->paginate(Config::get('setting.per_page'));

        return $credits;
    }

    public function getThreads(User $user)
    {
        $threads = $user->threads()->visible()->recent()->paginate(15);

        return $threads;
    }
}