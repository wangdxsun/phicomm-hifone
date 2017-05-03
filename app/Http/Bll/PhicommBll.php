<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 9:05
 */

namespace Hifone\Http\Bll;

use GuzzleHttp\Client;

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
                    throw new \Exception('验证码错误！');
                case 2:
                    throw new \Exception('验证码过期，请重新获取！');
                case 14:
                    throw new \Exception('账户已存在！');
                case 23:
                    throw new \Exception('验证码已被使用！');
                default:
                    throw new \Exception('服务器异常！', 500);
            }
        } else {
            throw new \Exception('服务器异常！', 500);
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
            throw new \Exception('手机号或密码错误');
        }
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
                    break;
                case 14:
                    throw new \Exception('该手机号已注册！');
                    break;
                default:
                    throw new \Exception('操作失败，请联系客服！');
            }
        }else{
            throw new \Exception('操作失败，请联系客服！');
        }
    }

    public function reset($phone, $password, $verifyCode)
    {
        $url = env('PHICLOUND_DOMAIN') . 'forgetpassword';
        $data = [
            'authorizationcode' => $this->getAccessCode(),
            'phonenumber' => $phone,
            'newpassword' => $password,
            'verificationcode' => $verifyCode
        ];
        $output = json_decode(curlPost($url, $data), true);
        if ($output){
            switch($output['error']){
                case 0:
                    return $output;
                case 1:
                    throw new \Exception('验证码错误！');
                case 2:
                    throw new \Exception('验证码已过期！');
                case 7:
                    throw new \Exception('您还未注册，请先注册！');
                case 32:
                    throw new \Exception('密码格式错误');
                case 50:
                    throw new \Exception('服务器异常！');
                default:
                    throw new \Exception($output['message']);
            }
        } else {
            throw new \Exception('密码重置失败!');
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
            throw new \Exception('验证码发送失败！');
        }
    }
}