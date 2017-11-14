<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/16
 * Time: 15:48
 */

namespace Hifone\Http\Controllers\App\V1;

use Auth;
use Hifone\Http\Bll\ChatBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Chat;
use Hifone\Models\User;

class ChatController extends AppController
{
    //私信列表
    public function chats(ChatBll $chatBll)
    {
        $chats = $chatBll->chats();
        return $chats;
    }

    //聊天记录
    public function messages(User $user, Chat $chat, ChatBll $chatBll)
    {
        $messages = $chatBll->recentMessages($user, $chat);
        return $messages;
    }

    public function store(User $user, ChatBll $chatBll)
    {
        if (Auth::user()->hasRole('NoComment')) {
            throw new \Exception('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new \Exception('对不起，你所在的用户组无法发言');
        }
        $res = $chatBll->newMessage($user);
        return $res;
    }

}