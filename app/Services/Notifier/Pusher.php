<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/2/7
 * Time: 14:56
 */

namespace Hifone\Services\Notifier;

class Pusher
{
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
     *
     */
    public function push($uid, $data, $outline = '', $msg_type = '1')
    {
        if (empty($uid)) {
            return;
        }
        $ticker = $msg_type == '0' ? 'ticker' : '';

        //云服务参数
        $parameters = [
            'callbackmsginfo' => '',
            'callbackurl' => '',
            'coverimg' => '',
            'mode' => '1',//0.develop, 1.production
            'msgcontent' => json_encode($data),
            'msgkind' => '7',//
            'msgtype' => $msg_type,
            'outline' => $outline,
            'saveRecord' => '1',
            'source' => '1',
            'ticker' => $ticker,
            'timestamp' => date('Y-m-d H:i', strtotime('now')),
            'title' => $data['title'],
            'uid' => $uid,
            'url' => '',
        ];
        //测试环境 114.141.173.53外网 192.168.43.111内网
        $json = curlPost(env('PHIDELIVER'), $parameters);
        $output = json_decode($json, true);
        if (is_null($output)) {//html
            $message = "Required String parameter 'uid' is not present";
            \Log::error($message, ['uid' => $uid]);
        } elseif (0 != $output['error']) {
            $message = 'error : ' . $output['error'] . 'message : ' . $output['message'];
            \Log::error($message, ['error' => $output['error']]);
        }

        return $output;
    }
}