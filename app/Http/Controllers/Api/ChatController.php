<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/16
 * Time: 15:48
 */

namespace Hifone\Http\Controllers\Api;

use Auth;
use Hifone\Exceptions\HifoneException;
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
        if (Auth::user()->hasRole('NoComment')) {
            throw new HifoneException('对不起，你已被管理员禁止发言');
        } elseif (!Auth::user()->can('manage_threads') && Auth::user()->score < 0) {
            throw new HifoneException('对不起，你所在的用户组无法发言');
        }
        $res = $chatBll->newMessage($user);

        return $res;
    }

}