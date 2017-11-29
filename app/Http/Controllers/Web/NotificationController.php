<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\ChatBll;
use Hifone\Http\Bll\NotificationBll;

class NotificationController extends WebController
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