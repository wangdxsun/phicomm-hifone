<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/16
 * Time: 15:34
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Events\Chat\NewChatMessageEvent;
use Hifone\Models\Chat;
use Hifone\Models\User;
use Input;

class ChatBll extends BaseBll
{
    public function chats()
    {
        $messages = Chat::my()->latest()->get()->unique('from_to')->load(['from', 'to']);
        \Auth::user()->notification_chat_count = 0;
        \Auth::user()->save();

        return $messages;
    }

    public function messages(User $user)
    {
        return Chat::chatWith($user)->with('from', 'to')->latest()->paginate();
    }

    public function newMessage(User $to)
    {
        $from = \Auth::user();
        if (Input::has('image')) {
            $image = Input::get('image');
            $res = dispatch(new UploadBase64ImageCommand($image));
            $message = "<img src='{$res["filename"]}' class='message_image'/>";
            event(new NewChatMessageEvent($from, $to, $message));
        }
        if (Input::has('imageUrl')) {
            $imageUrl = Input::get('imageUrl');
            $message = "<img src='{$imageUrl}' class='message_image'/>";
            event(new NewChatMessageEvent($from, $to, $message));
        }
        if (Input::has('message')) {
            $message = Input::get('message');
//            $message = app('parser.markdown')->convertMarkdownToHtml(app('parser.at')->parse(request('message')));
            event(new NewChatMessageEvent($from, $to, $message));
        }
        $to->increment('notification_chat_count', 1);
        $to->increment('notification_count', 1);
        return [
            'from' => $from->username,
            'to' => $to->username,
            'message' => $message,
        ];
    }
}