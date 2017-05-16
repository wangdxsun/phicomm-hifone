<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/16
 * Time: 15:34
 */

namespace Hifone\Http\Bll;

use Hifone\Events\Chat\NewChatMessageEvent;
use Hifone\Models\Chat;
use Hifone\Models\User;

class ChatBll extends BaseBll
{
    public function chats()
    {
        $messages = Chat::my()->latest()->get()->unique('from_to')->load(['from', 'to']);

        return $messages;
    }

    public function messages(User $user)
    {
        return Chat::chatWith($user)->with('from', 'to')->latest()->paginate();
    }

    public function newMessage(User $to)
    {
        $from = \Auth::user();
        $message = request('message');

        event(new NewChatMessageEvent($from, $to, $message));

        return [
            'from' => $from->username,
            'to' => $to->username,
            'message' => $message,
        ];
    }
}