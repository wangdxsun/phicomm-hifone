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
        $to->increment('notification_count', 1);
        //TODO 友盟消息推送
        $data = array(
            'message' => $messages[0],
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
                $messages[] = app('parser.emotion')->parse(request('message'));
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

    /**
     * 参数说明：
     * callbackmsginfo	回调消息信息	string	表征推送消息，告知推送方推送结果，与callbackurl一起使用
     * callbackurl	回调url	string	根据该url告知推送方推送结果
     * coverimg	封面图片	string	封面图片
     * mode	证书模式	string	0：测试（开发）模式, 1：生产模式；默认为1. 注：该字段IOS App为必选，安卓App 为非必选
     * msgcontent	推送消息内容	string	长度:400个字符,200个汉字；编码:URLEncord json格式
     * msgkind	消息类型	string	一种APP的不同的应用消息，默认为0，参考消息类型列表j接口
     * msgtype	推送消息类型	string	0.通知，1.消息。注：该字段为1时，安卓为消息推送，IOS为静默推送
     * outline	推送消息概要	string	长度待定
     * saveRecord	是否保存推送历史记录	string	可选参数; 0：不保存 , 1：保存 , 默认为1
     * source	推送平台	string	1.斐讯路由APP; 2.空气猫APP; 40.净水器APP; 50.共享WIFI APP；60.空净
     * ticker	Android设备上状态栏的通知显示	string	当消息类型为通知类型时，此字段为必须项
     * timestamp	时间戳	string	时间戳
     * title	推送消息标题	string	25个汉字，50个字符
     * uid	接收消息的斐讯云账户ID	string	等于all时 会进行广播
     * url	信息的URL 	string  私信类消息的对话页面链接   帖子类消息的帖子链接  系统提示类消息链接为空
     */
    public function pushMessage($data)
    {
        $ticker = '';
        if($data['msg_type'] == '0'){
            $ticker = 'ticker';
        }

        $json_message = '{"content":"' . $data['message'] . '"}';
        $parameters = array(
            'callbackmsginfo' => '',
            'callbackurl' => '',
            'coverimg' => '',
            'mode' => '0',//0.develop, 1.production
            'msgcontent' => $json_message,
            'msgkind' => '0',
            'msgtype' => $data['msg_type'],
            'outline' => $data['outline'],
            'saveRecord' => '0',
            'source' => '1',
            'ticker' => $ticker,
            'timestamp' => date('Y-m-d H:i', strtotime('now')),
            'title' => $data['title'],
            'uid' => $data['uid'],
            'url' => $data['url'],
        );
        //测试环境 114.141.173.53内网 192.168.43.111外网
        $json = curlPost(env('PHIDELIVER'), $parameters);
        $output = json_decode($json, true);

        return $output;

    }
}