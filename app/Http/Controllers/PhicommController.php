<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/1/18
 * Time: 20:49
 */

namespace Hifone\Http\Controllers;

use Hifone\Models\Provider;
use Hifone\Models\User;
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
        $this->validate($request, [
            'phone' => 'required|phone',
            'password' => 'required',
            'verifyCode' => 'required',
        ]);
        $password = strtoupper(md5($request->get('password')));
        $this->phicommRegister($request->phone, $password, $request->verifyCode);
        $phicommId = $this->phicommLogin($request->phone, $password);
        session(['phicommId', $phicommId]);
        return success();
    }

    private function phicommRegister($phone, $password, $verifyCode)
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
        if($output){
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

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'phicommToken' => 'required_without:phone',
            'phone' => 'required_without:phicommToken|phone',
            'password' => 'required_with:phone',
        ]);
        $phicommToken = $request->get('phicommToken');
        $phone = $request->get('phone');
        $password = strtoupper(md5($request->get('password')));
        $phicommId = $phicommToken ? $this->getIdFromToken($phicommToken) : $this->phicommLogin($phone, $password);
        $user = User::findUserByPhicommId($phicommId);
        if ($user) {
            \Auth::loginUsingId($user->id);
            return success(['bind' => 1]);
        } else {
            session(['phicommId', $phicommId]);
            return success(['bind' => 0]);
        }
    }

    private function phicommLogin($phone, $password)
    {
        $accessCode = $this->getAccessCode();
        $data = ['authorizationcode' => $accessCode, 'phonenumber' => $phone, 'password' => $password];
        $url = env('PHICLOUND_DOMAIN') . 'login';
        $output = json_decode(curlPost($url, $data), true);
        if ($output['error'] > 0) {
            throw new \Exception('用户名或密码错误');
        }
        return $output['uid'];
    }

    public function getCreate()
    {
        return view('phicomm.create');
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
        $rel = json_decode(curlGet($url), true);
        if ($rel || $rel['error'] > 0) {
            throw new \Exception('验证码发送失败！');
        }
    }

    /**
     * 检测手机号是否已注册
     */
    private function checkPhoneAvailable($phone){
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

    private function getAccessCode()
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

    private function getIdFromToken($token) {
        $tokens = explode('.', $token);
        $tokenInfo = json_decode(base64_decode($tokens[1]), true);
        return $tokenInfo['uid'];
    }
}