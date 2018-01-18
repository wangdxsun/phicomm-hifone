<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 9:05
 */

namespace Hifone\Http\Bll;

use Carbon\Carbon;
use Hifone\Events\User\AppUserWasActiveEvent;
use Hifone\Events\User\H5UserWasActiveEvent;
use Hifone\Events\User\UserWasActiveEvent;
use Hifone\Events\User\WebUserWasActiveEvent;
use Hifone\Models\BaseModel;
use Auth;

class BaseBll
{
    public function isContainsImageOrUrl($str)
    {
        if (substr_count($str,'<a') > 0 && (substr_count($str,'<a') != substr_count($str, '@'))) {
            return true;
        } elseif (substr_count($str,'class="face"') != substr_count($str,'<img')) {
            return true;
        } else {
            return false;
        }
    }

    public function updateOpLog(BaseModel $model, $operation, $reason = null)
    {
        $operator = $operation == '自动审核通过' ? 0 : Auth::id();
        $model->last_op_user_id = $operator;
        $model->last_op_time = Carbon::now()->toDateTimeString();
        $reason && $model->last_op_reason = $reason;
        $model->save();
        $logData['user_id'] = $operator;
        $logData['operation'] = $operation;
        $logData['reason'] = $reason;
        $model->logs()->create($logData);
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
     *
     * $data['type'] 消息类型 社区定义如下：
     * 1001 —— 评论帖子
     * 1002 —— 回复帖子的评论
     * 1003 —— 管理员操作 （帖子置顶、高亮、提升到首页）
     * 1004 —— 私信
     * 1005 —— 系统提示
     */
    public function pushMessage($data)
    {
        $ticker = '';
        if($data['msg_type'] == '0'){
            $ticker = 'ticker';
        }

        //根据type构造不同message_content封装到$data
        $array_message = [
            "content" => $data['message'],
            "type" => $data['type'],
            "source" => '1',
            "producer" => '2',
            "isBroadcast" => '0',
            "isMulticast" => '0',
            "avatar" => $data['avatar'],
            "title" => $data['title'],
            "time" => $data['time'],
            "userId" => $data['userId'],
        ];
        if ("1001" == $data['type'] || "1002" == $data['type']) {
            $array_message += ["replyId" => $data['replyId']];
        }

        $json_message = json_encode($array_message);

        $parameters = array(
            'callbackmsginfo' => '',
            'callbackurl' => '',
            'coverimg' => '',
            'mode' => '0',//0.develop, 1.production
            'msgcontent' => $json_message,
            'msgkind' => '0',
            'msgtype' => $data['msg_type'],
            'outline' => $data['outline'],
            'saveRecord' => '1',
            'source' => '1',
            'ticker' => $ticker,
            'timestamp' => date('Y-m-d H:i', strtotime('now')),
            'title' => $data['title'],
            'uid' => $data['uid'],
            'url' => '',
        );
        //测试环境 114.141.173.53外网 192.168.43.111内网
        $json = curlPost(env('PHIDELIVER'), $parameters);
        $output = json_decode($json, true);

        return $output;
    }

    public function appUpdateActiveTime()
    {
        //点击node，统计活跃参与用户数
        if (Auth::check()) {
            $activeDate = app('session')->get('app_user_active_date');
            if (!$activeDate || $activeDate != date('Ymd')) {
                event(new AppUserWasActiveEvent(Auth::user()));
                app('session')->put('app_user_active_date', date('Ymd'));
            }
        }
    }


    public function h5UpdateActiveTime()
    {
        //点击node，统计活跃参与用户数
        if (Auth::check()) {
            $activeDate = app('session')->get('user_active_date');
            if (!$activeDate || $activeDate != date('Ymd')) {
                event(new H5UserWasActiveEvent(Auth::user()));
                app('session')->put('user_active_date', date('Ymd'));
            }
        }
    }


    public function webUpdateActiveTime()
    {
        //点击node，统计活跃参与用户数
        if (Auth::check()) {
            $activeDate = app('session')->get('web_user_active_date');
            if (!$activeDate || $activeDate != date('Ymd')) {
                event(new WebUserWasActiveEvent(Auth::user()));
                app('session')->put('web_user_active_date', date('Ymd'));
            }
        }
    }
}