<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/2/7
 * Time: 14:56
 */

namespace Hifone\Services\Notifier;


use Hifone\Exceptions\HifoneException;

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
    public function push($data)
    {
        $ticker = '';
        if($data['msg_type'] == '0'){
            $ticker = 'ticker';
        }

        $reverseEmotionAndImage = app('parser.emotion')->reverseParseEmotionAndImage($data['message']);
        $type = $this->getType($data['type']);

        //社区业务参数 根据type构造不同message_content封装到$data
        $array_message = [
            "content" => mb_substr($reverseEmotionAndImage, 0, 100),
            "type" => $type,
            "source" => '1',
            "producer" => '2',
            "isBroadcast" => '0',
            "isMulticast" => '0',
            "avatar" => $data['avatar'],
            "title" => $data['title'],
            "time" => $data['time'],
            "userId" => $data['userId'],
        ];
        //用户关注无需提供thread_id和reply_id
        //否则接口根据reply_id跳当前评论，不提供跳帖子详情
        if ('user_follow' != $data['type'] && 'chat' != $data['type']) {
            $array_message['threadId'] = $data['threadId'];
            if ("reply_reply" == $data['type'] || "reply_mention" == $data['type']
                || "reply_like" == $data['type'] || "reply_pin" == $data['type']) {
                $array_message['replyId'] = $data['replyId'];
            }
        }

        $json_message = json_encode($array_message);

        //云服务参数
        $parameters = array(
            'callbackmsginfo' => '',
            'callbackurl' => '',
            'coverimg' => '',
            'mode' => '1',//0.develop, 1.production
            'msgcontent' => $json_message,
            'msgkind' => '7',//
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

    /**
     * @param $typeStr
     *    1001 评论
     *    1002 回复/帖子@我/回复@我
     *    1003 收到的关注
     *    1004 私信
     *    1005 收到的赞（赞帖子、赞评论/回复）
     *    1006 收到收藏
     *    1007 管理员置顶（帖子、评论/回复）
     *    1008 管理员加精华
     * @return string
     * @throws HifoneException
     */
    protected function getType($typeStr)
    {
        switch ($typeStr){
            case 'thread_new_reply'://评论帖子 跳帖子详情 提供
                return '1001';
            case 'reply_reply'://回复 跳当前评论
                return '1002';
            case 'reply_mention'://回复@我 跳当前评论
                return '1002';
            case 'thread_mention'://帖子@我 跳帖子详情
                return '1002';
            case 'user_follow'://关注用户 跳粉丝列表
                return '1003';
            case 'chat'://私信 跳聊天记录
                return '1004';
            case 'thread_like'://赞帖子 跳帖子详情
            case 'reply_like'://赞回复 跳当前评论
                return '1005';
            case 'thread_favorite'://收藏（帖子） 跳帖子详情
                return '1006';
            case 'thread_pin'://置顶帖子 跳帖子详情
            case 'reply_pin'://置顶评论 跳当前评论
                return '1007';
            case 'thread_mark_excellent'://加精华 跳帖子详情
                return '1008';
            default :
                throw new HifoneException("推送类型 $typeStr 不支持");
        }
    }
}