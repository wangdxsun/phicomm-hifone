<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/16
 * Time: 15:48
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\ChatBll;
use Hifone\Http\Bll\NotificationBll;
use Hifone\Http\Controllers\App\AppController;

class NotificationController extends AppController
{
    public function index(NotificationBll $notificationBll, ChatBll $chatBll)
    {
        $replies = $notificationBll->reply();
        $ats = $notificationBll->at();
        $messages = $chatBll->chats();
        $systems = $notificationBll->system();
        $notifications = [
            'replies' => $replies,
            'ats' => $ats,
            'messages' => $messages,
            'systems' => $systems,
        ];
        return $notifications;
    }

    //关注动态
    public function watch(NotificationBll $notificationBll)
    {
        $watches = $notificationBll->watch();

        return $watches;
    }

    public function reply(NotificationBll $notificationBll)
    {
        $replies = $notificationBll->reply();

        return $replies;
    }

    public function at(NotificationBll $notificationBll)
    {
        $ats = $notificationBll->at();

        return $ats;
    }

    public function system(NotificationBll $notificationBll)
    {
        $systems = $notificationBll->system();

        return $systems;
    }

    //好友圈（新关注动态）
    public function moment(NotificationBll $notificationBll)
    {
        $moments = $notificationBll->moment();

        return $moments;
    }

}