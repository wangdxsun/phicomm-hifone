<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 9:05
 */

namespace Hifone\Http\Bll;

use GuzzleHttp\Client;
use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\User;
use Auth;
use Hifone\Services\Filter\WordsFilter;
use Session;

class PhicommBll extends BaseBll
{
    /**
     * @var Client
     */
    private $http;

    public function __construct()
    {
        $this->http = new Client(['base_uri' => env('PHICLOUND_DOMAIN')]);
    }

    public function register($phone, $password, $verifyCode)
    {
        $data = [
            'authorizationcode' => $this->getAccessCode(),
            'password' => $password,
            'phonenumber' => $phone,
            'registersource' => env('PHICLOUND_CLIENT_ID'),
            'verificationcode' => $verifyCode
        ];
        $url = $url = env('PHICLOUND_DOMAIN') . 'account';
        $output = json_decode(curlPost($url, $data), true);
        if ($output) {
            switch($output['error']){
                case 0:
                    return $output;break;
                case 1:
                    throw new HifoneException('验证码错误！');
                case 2:
                    throw new HifoneException('验证码过期，请重新获取！');
                case 14:
                    throw new HifoneException('账户已存在！');
                case 23:
                    throw new HifoneException('验证码已被使用！');
                default:
                    throw new HifoneException('服务器异常！', 500);
            }
        } else {
            throw new HifoneException('服务器异常！', 500);
        }
    }

    public function login($phone, $password)
    {
        $data = [
            'authorizationcode' => $this->getAccessCode(),
            'phonenumber' => $phone,
            'password' => $password
        ];
        $url = env('PHICLOUND_DOMAIN') . 'login';
        $output = json_decode(curlPost($url, $data), true);
        if ($output['error'] > 0) {
            throw new HifoneException('手机号或密码错误');
        }

        Session::set('access_token', $output['access_token']);
        Session::set('phicommId', $output['uid']);

        return $output['uid'];
    }

    public function getAccessCode()
    {
        $data = [
            'client_id' => env('PHICLOUND_CLIENT_ID'),
            'client_secret' => env('PHICLOUND_CLIENT_SECRET'),
            'response_type' => 'code',
            'scope' => 'write',
        ];
        $url = env('PHICLOUND_DOMAIN') . 'authorization?' . http_build_query($data);
        $output = json_decode(curlGet($url), true);

        return $output['authorizationcode'];
    }

    public function getIdFromToken($token) {
        $tokens = explode('.', $token);
        $tokenInfo = json_decode(base64_decode($tokens[1]), true);

        return $tokenInfo['uid'];
    }

    /**
     * 检测手机号是否已注册
     */
    public function checkPhoneAvailable($phone){
        $accessCode = $this->getAccessCode();
        $url = env('PHICLOUND_DOMAIN') . 'checkPhonenumber?authorizationcode=' . $accessCode . '&phonenumber=' . $phone;
        $output = json_decode(curlGet($url), true);
        if($output){
            switch($output['error']){
                case 0:
                    return true;
                case 14:
                    throw new HifoneException('该手机号已注册！');
                default:
                    throw new HifoneException('操作失败，请联系客服！', 500);
            }
        }else{
            throw new HifoneException('操作失败，请联系客服！', 500);
        }
    }

    public function reset($phone, $password, $verify)
    {
        $url = env('PHICLOUND_DOMAIN') . 'forgetpassword';
        $data = [
            'authorizationcode' => $this->getAccessCode(),
            'phonenumber' => $phone,
            'newpassword' => $password,
            'verificationcode' => $verify
        ];
        $output = json_decode(curlPost($url, $data), true);
        if ($output){
            switch($output['error']){
                case 0:
                    return $output;
                case 1:
                    throw new HifoneException('验证码错误！');
                case 2:
                    throw new HifoneException('验证码已过期！');
                case 7:
                    throw new HifoneException('您还未注册，请先注册！');
                case 32:
                    throw new HifoneException('密码格式错误');
                case 50:
                    throw new HifoneException('服务器异常！', 500);
                default:
                    throw new HifoneException($output['message']);
            }
        } else {
            throw new HifoneException('密码重置失败!', 500);
        }
    }

    public function sendVerifyCode($phone)
    {
        $accessCode = $this->getAccessCode();
        $data = [
            'authorizationcode' => $accessCode,
            'phonenumber' => $phone,
            'verificationtype' => 0,
        ];
        $url = env('PHICLOUND_DOMAIN') . 'verificationCode?' . http_build_query($data);
        $res = json_decode(curlGet($url), true);
        if ($res && $res['error'] > 0) {
            throw new HifoneException('验证码发送失败！');
        }
    }

    public function bind(WordsFilter $wordsFilter)
    {
        $userData = [
            'phicomm_id' => Auth::phicommId(),
            'username' => request('username'),
            'password' => str_random(32),
            'regip' => getClientIp(),
        ];
        if (User::where('username', request('username'))->count() > 0) {
            throw new HifoneException('该昵称已被使用');
        }
        if (User::where('phicomm_id', $userData['phicomm_id'])->count() > 0) {
            throw new HifoneException('请勿重复关联');
        }
        if ($wordsFilter->filterWord(request('username')) || $wordsFilter->filterKeyWord(request('username'))) {
            throw new HifoneException('昵称包含被系统屏蔽字符');
        }
        $user = User::create($userData);//直接通过create返回的用户信息不全
        $user = User::find($user->id);
        $user->addToIndex();
        event(new UserWasAddedEvent($user));
        Auth::login($user);

        return $user;
    }

    public function upload($file)
    {
        $file = new \CURLFile($file);
        $data = [
            'file' => $file,
            'type' => 1,
        ];

        $token = Auth::token();
        $header = "Authorization:$token";
        $res = json_decode(curl_form_post(env('PORTRAIT'), $data, $header, 'POST'), true);

        if ($res) {
            switch($res['error']){
                case 0:
                    return $res;
                case 18:
                    throw new HifoneException('图片格式错误！');
                case 19:
                    throw new HifoneException('图片为空！');
                case 50:
                    throw new HifoneException('服务器异常！');
                default:
                    throw new HifoneException($res['message']);
            }
        } else {
            throw new HifoneException('服务器异常！');
        }
    }

    public function userInfo()
    {
        $url = env('PHICLOUND_DOMAIN').'accountDetail';
        $header = ["Authorization:".Auth::token()];
        $res = json_decode(curlGet($url, $header), true);
        if (array_get($res, 'error') == 0) {
            return $res['data'];
        }
        return null;
    }

    /**
     * 参数说明：
     * $msg_type 0，通知（会响）， 1，消息
     * source   1——论坛 2——商场  3——路由器  4——APP
     * title    消息列表中消息标题
     * outline  消息列表中消息概括
     * type     1001——评论帖子  1002——帖子评论回复  1003——管理员操作（帖子置顶、高亮、提升等）
     *          1004——私信  1005——系统提示（帖子审核通过、成为超级会员等）
     * in_title 消息详情的标题
     * message  消息内容
     * uid      接收消息的斐讯云账户ID
     * url      私信类消息的对话页面链接   帖子类消息的帖子链接  系统提示类消息链接为空
     *
     */
    public function pushMessage($msg_type, $title, $outline, $in_title, $type, $message, $uid, $url = null)
    {
        $ticker = '';
        if($msg_type == '0'){
            $ticker = 'ticker';
        }

        $json_data = '{"avatar":"avatarUrl","coverImg":"coverImgUrl","content":"' . $message . '","source":1,"time":"'
            . date('Y-m-d H:i', strtotime('now')) . '","title":"'.$in_title.'","type":' . $type . ',"url":"' . $url . '"}';
        $parameter = array(
            'msgtype' => $msg_type,
            'source' => '1',
            'ticker' => $ticker,
            'timestamp' => date('Y-m-d H:i', strtotime('now')),
            'uid' => $uid,
            'outline' => $outline,
            'msgcontent' => $json_data,
            'title' => $title,
            'url' => '',
            'coverimg' => '',
        );
        //$output = json_decode(curlPost(env('PHICLOUD_MESSAGE_PUSH'), $parameter),true);

        $output = json_decode(curlPost('https://phideliver.phicomm.com/PhiPushServiceV1/newmessage', $parameter),true);

        return $output;

    }
}