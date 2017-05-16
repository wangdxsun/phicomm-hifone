<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/16
 * Time: 15:48
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\ChatBll;
use Hifone\Models\User;

class ChatController extends ApiController
{
    public function chats(ChatBll $chatBll)
    {
        $chats = $chatBll->chats();

        return $chats;
    }

    public function messages(User $user, ChatBll $chatBll)
    {
        $messages = $chatBll->messages($user);

        return $messages;
    }

    public function store(User $user, ChatBll $chatBll)
    {
        $res = $chatBll->newMessage($user);

        return $res;
    }
}