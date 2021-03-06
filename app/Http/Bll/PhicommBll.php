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
use Illuminate\Support\Facades\Redis;
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
                    return $output['uid'];break;
                case 1:
                    throw new HifoneException('短信验证码有误');
                case 2:
                    throw new HifoneException('验证码过期，请重新获取');
                case 14:
                    throw new HifoneException('账户已存在');
                case 23:
                    throw new HifoneException('验证码已使用');
                default:
                    throw new HifoneException('服务器异常', 500);
            }
        } else {
            throw new HifoneException('服务器异常', 500);
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
            throw new HifoneException('账号或密码错误，请重新输入');
        }

        Session::set('access_token', $output['access_token']);
        Session::set('phicommId', $output['uid']);

        return $output;
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
        if ($output) {
            switch($output['error']) {
                case 0:
                    return true;
                case 14://“直接登录”前端做成链接
                    throw new HifoneException('该手机号已注册，请直接登录', 410);
                default:
                    throw new HifoneException('操作失败，请联系客服', 500);
            }
        } else {
            throw new HifoneException('操作失败，请联系客服', 500);
        }
    }

    /**
     * 检测手机号是否未注册，重置密码用，登录用
     * @param $phone
     * @return bool
     * @throws HifoneException
     */
    public function checkPhoneRegistered($phone)
    {
        $accessCode = $this->getAccessCode();
        $url = env('PHICLOUND_DOMAIN') . 'checkPhonenumber?authorizationcode=' . $accessCode . '&phonenumber=' . $phone;
        $output = json_decode(curlGet($url), true);
        if ($output) {
            switch($output['error']) {
                case 0:
                    throw new HifoneException('该手机号未注册');
                case 14:
                    return true;
                default:
                    throw new HifoneException('操作失败，请联系客服', 500);
            }
        } else {
            throw new HifoneException('操作失败，请联系客服', 500);
        }
    }

    public function reset($phone, $password, $verify)
    {
        //TODO 设置每日5次验证码验证错误次数上限 云账号做
        $url = env('PHICLOUND_DOMAIN') . 'forgetpassword';
        $data = [
            'authorizationcode' => $this->getAccessCode(),
            'phonenumber' => $phone,
            'newpassword' => $password,
            'verificationcode' => $verify
        ];
        $output = json_decode(curlPost($url, $data), true);
        if ($output){
            switch($output['error']) {
                case 0:
                    return $output;
                case 1:
                    throw new HifoneException('短信验证码有误');
                case 2:
                    throw new HifoneException('验证码已过期');
                case 7:
                    throw new HifoneException('您还未注册，请先注册');
                case 32:
                    throw new HifoneException('密码格式错误');
                case 50:
                    throw new HifoneException('服务器异常', 500);
                default:
                    throw new HifoneException($output['message']);
            }
        } else {
            throw new HifoneException('密码重置失败', 500);
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
        if ($res) {
            switch($res['error']) {
                case 0:
                    return $res;
                case 13:
                    throw new HifoneException('获取验证码失败');
                case 38:
                    throw new HifoneException('操作频繁，请1分钟后再试');
                case 39:
                    throw new HifoneException('验证码请求超出限制');
                default:
                    throw new HifoneException('服务器异常');
            }
        } else {
            throw new HifoneException('获取验证码失败');
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
            throw new HifoneException('昵称含有被屏蔽字符');
        }
        $user = User::create($userData);//直接通过create返回的用户信息不全
        $user = User::find($user->id);
        event(new UserWasAddedEvent($user));
        Auth::login($user);

        return $user;
    }

    public function upload($file)
    {
        if (!is_object($file)) {
            $file = new \CURLFile($file);
        }
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
                case 5:
                    //若token过期刷新后重新上传头像
                    $this->refreshToken();
                    $this->upload($file);
                    break;
                case 18:
                    throw new HifoneException('图片格式错误');
                case 19:
                    throw new HifoneException('图片为空');
                case 50:
                    throw new HifoneException('服务器异常');
                default:
                    throw new HifoneException($res['message']);
            }
        } else {
            throw new HifoneException('服务器异常');
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

    protected function refreshToken()
    {
        $refresh_token = Auth::user()->refresh_token;
        $url = env('PHICLOUND_DOMAIN') . 'token?authorizationcode=' . $this->getAccessCode() . '&grant_type=refresh_token';
        $header = ["Authorization:".$refresh_token];
        $res = json_decode(curl_get($url, $header), true);

        if ($res) {
            switch($res['error']){
                case 0:
                    //h5 web请求header不带token请求，刷新session中token足够
                    Session::set('access_token', $res['access_token']);
                    break;
                case 5:
                    throw new HifoneException('登录失效，请重新登录');
                default:
                    throw new HifoneException($res['message']);
            }
        } else {
            throw new HifoneException('服务器异常');
        }
    }

}