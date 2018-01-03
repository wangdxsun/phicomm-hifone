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

    //for h5
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
        $to->increment('notification_chat_count', 1);
        //友盟消息推送
        $data = array(
            'message' => $messages[0],
            'type' => '1004',
            'msg_type' => '0',//推送消息类型 0.通知,1.消息
            'outline' => substr($messages[0], 0, 26),
            'title' => $from->username,
            'uid' => $to->phicomm_id,
            'url' => route('app.chat.message', ['user' => $from->id, 'scope' => 'after']),
        );
        $this->pushMessage($data);

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
                $message = app('parser.at')->parse(request('message'));
                $message = app('parser.emotion')->parse($message);
                $messages[] = app('parser.markdown')->convertMarkdownToHtml($message);
            } else {
                $messages[] = app('parser.emotion')->parse(e(request('message')));
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
                    'from_to' => $from->id * $to->id + $from->id + $to->id,
                    'message' => $message,
                    'created_at'    => Carbon::now()->toDateTimeString(),
                    'updated_at'    => Carbon::now()->toDateTimeString(),
                ];
            }
        }
        Chat::insert($insert);//批量创建
    }

}