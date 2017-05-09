<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 20:53
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\NotificationBll;

class NotificationController extends ApiController
{
    public function index(NotificationBll $bll)
    {
//        $bll->thread();
        $notifications = $bll->thread();

        return $notifications;
    }
}