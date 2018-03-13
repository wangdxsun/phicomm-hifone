<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/16
 * Time: 15:34
 */

namespace Hifone\Http\Bll;

use Carbon\Carbon;
use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Events\Chat\NewChatMessageEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\Chat;
use Hifone\Models\User;
use Input;
use Auth;

class ChatBll extends BaseBll
{
    public function chats()
    {
        $chatIds = Chat::my()->selectRaw('max(id) as id')->groupBy('from_to')->pluck('id');
        $chats = Chat::whereIn('id', $chatIds)->with(['from', 'to'])->recent()->paginate();
        foreach ($chats as $chat) {
            $chat->message = app('parser.emotion')->reverseParseEmotionAndImage($chat->message);
        }
        Auth::user()->notification_chat_count = 0;
        Auth::user()->save();

        return $chats;
    }

    public function chatsApp(Chat $chat)
    {
        $chatIds = Chat::my()->after($chat)->selectRaw('max(id) as id')->groupBy('from_to')->pluck('id');
        $chats = Chat::whereIn('id', $chatIds)->with(['from', 'to'])->recent()->paginate();
        foreach ($chats as $chat) {
            $chat->message = app('parser.emotion')->reverseParseEmotionAndImage($chat->message);
        }
        Auth::user()->notification_chat_count = 0;
        Auth::user()->save();

        return $chats;
    }

    //for h5 and new web
    public function messages(User $user)
    {
        return Chat::chatWith($user)->with('from', 'to')->latest()->paginate();
    }

    //for app
    public function recentMessages(User $user, $scope, Chat $chat)
    {
        if ($scope == 'after') {
            return Chat::chatWith($user)->after($chat)->with('from', 'to')->latest()->paginate();
        } else {
            return Chat::chatWith($user)->before($chat)->with('from', 'to')->latest()->paginate();
        }
    }

    public function newMessage(User $to)
    {
        $from = Auth::user();

        $messages = $this->getMessages();
        event(new NewChatMessageEvent($from, $to, $messages[0]));
        return [
            'from' => $from->username,
            'to' => $to->username,
            'message' => $messages[0],
        ];
    }

    public function getMessages()
    {
        $messages = [];
        if (Input::has('image')) {
            $image = Input::get('image');
            $res = dispatch(new UploadBase64ImageCommand($image));
            $messages[] = "<img src='{$res["filename"]}' class='message_image'/>";
        }
        if (Input::has('imageUrl')) {
            $imageUrl = Input::get('imageUrl');
            $messages[] = "<img src='{$imageUrl}' class='message_image'/>";
        }
        if (Input::has('message')) {
            if (Auth::user()->can('manage_threads')) {
                $message = app('parser.link')->parse(request('message'));
                $message = app('parser.at')->parse($message);
                $messages[] = app('parser.emotion')->parse($message);
            } else {
                $messages[] = app('parser.emotion')->parse(e(request('message')));
            }
        }
        if (count($messages) == 0) {
            throw new HifoneException('私信内容不能为空');
        }

        return $messages;
    }

}