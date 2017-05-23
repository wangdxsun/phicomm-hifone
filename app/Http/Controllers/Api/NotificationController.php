<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/8
 * Time: 20:53
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\ChatBll;
use Hifone\Http\Bll\NotificationBll;

class NotificationController extends ApiController
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

    public function watch(NotificationBll $bll)
    {
        $watches = $bll->watch();

        return $watches;
    }

    public function reply(NotificationBll $bll)
    {
        $replies = $bll->reply();

        return $replies;
    }

    public function at(NotificationBll $bll)
    {
        $ats = $bll->at();

        return $ats;
    }

    public function system(NotificationBll $bll)
    {
        $systems = $bll->system();

        return $systems;
    }
}