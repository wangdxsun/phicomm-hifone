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

    public function atWithQA(NotificationBll $notificationBll)
    {
        $ats = $notificationBll->atWithQA();

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

    public function qa(NotificationBll $notificationBll)
    {
        $systems = $notificationBll->qa();

        return $systems;
    }

}