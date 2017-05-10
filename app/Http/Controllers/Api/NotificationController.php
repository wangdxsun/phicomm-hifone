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
        $replies = $bll->reply();
        $ats = $bll->at();
        $messages = $bll->message();
        $systems = $bll->system();
        $notifications = [
            'replies' => $replies,
            'ats' => $ats,
            'messages' => $messages,
            'systems' => $systems,
        ];

        return $notifications;
    }

    public function watch(NotificationBll $bll)
    {
        $watches = $bll->watch();

        return $watches;
    }
}