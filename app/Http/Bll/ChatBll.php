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
use Hifone\Models\Chat;
use Hifone\Models\User;
use Input;
use Auth;

class ChatBll extends BaseBll
{
    public function chats()
    {
        $chatIds = Chat::my()->selectRaw('max(id) as id')->groupBy('from_to')->pluck('id');
        $messages = Chat::whereIn('id', $chatIds)->with(['from', 'to'])->recent()->paginate();
        Auth::user()->notification_chat_count = 0;
        Auth::user()->save();

        return $messages;
    }

    public function messages(User $user)
    {
        return Chat::chatWith($user)->with('from', 'to')->latest()->paginate();
    }

    public function newMessage(User $to)
    {
        $from = Auth::user();
        $messages = $this->getMessages();
        event(new NewChatMessageEvent($from, $to, $messages[0]));
        $to->increment('notification_chat_count', 1);
        $to->increment('notification_count', 1);
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
                $messages[] = app('parser.markdown')->convertMarkdownToHtml(app('parser.at')->parse(request('message')));
            } else {
                $messages[] = Input::get('message');
            }
        }
        return $messages;
    }

    public function batchNewMessage($toUsers)
    {
        $from = Auth::user();
        $insert = [];
        $messages = $this->getMessages();
        foreach ($toUsers as $to) {
            foreach ($messages as $message) {
                $insert[] = [
                    'from_user_id' => $from->id,
                    'to_user_id' => $to->id,
                    'from_to' => $from->id * $to->id,
                    'message' => $message,
                    'created_at'    => Carbon::now()->toDateTimeString(),
                    'updated_at'    => Carbon::now()->toDateTimeString(),
                ];
            }
        }
        Chat::insert($insert);//批量创建

    }
}