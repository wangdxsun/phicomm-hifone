<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/9
 * Time: 8:47
 */

namespace Hifone\Http\Controllers\Api;

use Auth;
use Hifone\Http\Bll\FollowBll;
use Hifone\Http\Bll\UserBll;

class UserController extends ApiController
{
    public function index(UserBll $userBll)
    {

    }

    public function follows(FollowBll $followBll)
    {
        $follows = $followBll->follows(Auth::user());

        return $follows;
    }

    public function followers(FollowBll $followBll)
    {
        $followers = $followBll->followers(Auth::user());

        return $followers;
    }
}