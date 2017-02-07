<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/1/18
 * Time: 20:49
 */

namespace Hifone\Http\Controllers;

use Hifone\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class PhicommController extends Controller
{
    public function getRegister()
    {
        return $this->view('phicomm.register')
            ->withCaptchaRegisterDisabled(Config::get('setting.captcha_register_disabled'))
            ->withCaptcha(route('captcha', ['random' => time()]))
            ->withPageTitle(trans('hifone.login.login'));
    }

    public function postRegister(Request $request)
    {

    }

    public function getLogin()
    {
        $providers = Provider::recent()->get();

        return $this->view('phicomm.login')
            ->withCaptchaLoginDisabled(Config::get('setting.captcha_login_disabled'))
            ->withCaptcha(route('captcha', ['random' => time()]))
            ->withConnectData(Session::get('connect_data'))
            ->withProviders($providers)
            ->withPageTitle(trans('hifone.login.login'));
    }

    public function postLogin()
    {

    }

    /**
     * 发送短信验证码
     */
    public function sendVerifyCode($phone, $codeType = 0)
    {
        $this->checkPhoneAvailable($phone);
        $accessCode = $this->getAccessCode();
        $data = [
            'authorizationcode' => $accessCode,
            'phonenumber' => $phone,
            'verificationtype' => $codeType,
        ];
        $url = env('PHICLOUND_DOMAIN') . 'verificationCode?' . http_build_query($data);
        $rel = json_decode($this->curlGet($url), true);
        if ($rel) {
            if ($rel['error'] > 0) {
                throw new \Exception('验证码发送失败！');
            }
        } else {
            throw new \Exception('验证码发送失败！');
        }
    }

    /**
     * 检测手机号是否已注册
     */
    private function checkPhoneAvailable($phone){
        $accessCode = $this->getAccessCode();
        $url = env('PHICLOUND_DOMAIN') . 'checkPhonenumber?authorizationcode=' . $accessCode . '&phonenumber=' . $phone;
        $output = json_decode($this->curlGet($url), true);
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

    private function getAccessCode()
    {
        $data = [
            'client_id' => env('PHICLOUND_CLIENT_ID'),
            'client_secret' => env('PHICLOUND_CLIENT_SECRET'),
            'response_type' => 'code',
            'scope' => 'write',
        ];
        $url = env('PHICLOUND_DOMAIN') . 'authorization?' . http_build_query($data);
        $output = json_decode($this->curlGet($url), true);
        return $output['authorizationcode'];
    }

    private function curlGet($url, $header=null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if(!empty($header)){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    private function curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}