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
use Auth;

class ChatBll extends BaseBll
{
    public function chats()
    {
        $messages = Chat::my()->latest()->get()->unique('from_to')->load(['from', 'to']);
        Auth::user()->notification_chat_count = 0;
        Auth::user()->save();

        return $messages;
    }

    //for h5
    public function messages(User $user)
    {
        return Chat::chatWith($user)->with('from', 'to')->latest()->paginate();
    }

    //for app
    public function recentMessages(User $user, Chat $chat)
    {
        return Chat::chatWith($user)->new($chat)->with('from', 'to')->latest()->paginate();
    }

    public function newMessage(User $to)
    {
        $from = Auth::user();
        $message = $this->parseMessageBody();
        event(new NewChatMessageEvent($from, $to, $message));
        $to->increment('notification_chat_count', 1);
        $to->increment('notification_count', 1);
        return [
            'from' => $from->username,
            'to' => $to->username,
            'message' => $message,
        ];
    }

    public function parseMessageBody()
    {
        $message = '';
        if (Input::has('image')) {
            $image = Input::get('image');
            $res = dispatch(new UploadBase64ImageCommand($image));
            $message = "<img src='{$res["filename"]}' class='message_image'/>";
        }
        if (Input::has('imageUrl')) {
            $imageUrl = Input::get('imageUrl');
            $message = "<img src='{$imageUrl}' class='message_image'/>";
        }
        if (Input::has('message')) {
            $message = Input::get('message');
//            $message = app('parser.markdown')->convertMarkdownToHtml(app('parser.at')->parse(request('message')));
        }
        return $message;
    }

    public function batchNewMessage($toUsers)
    {
        $from = Auth::user();
        $insert = [];
        foreach ($toUsers as $to) {
            if (empty($to)) {
                continue;
            }
            $message = $this->parseMessageBody();
            $insert[] = [
                'from_user_id' => $from->id,
                'to_user_id' => $to->id,
                'from_to' => $from->id * $to->id,
                'message' => $message,
            ];
        }
        Chat::insert($insert);//批量创建

    }
}