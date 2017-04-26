<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/1/18
 * Time: 20:49
 */

namespace Hifone\Http\Controllers;

use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Hashing\PasswordHasher;
use Hifone\Models\Provider;
use Hifone\Models\User;
use Illuminate\Http\Request;
use Config;
use Session;
use Redirect;
use Auth;
use Input;

class PhicommController extends Controller
{
    protected $hasher;

    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;
        $this->middleware('guest', ['except' => ['logout', 'getLogout']]);
        parent::__construct();
    }

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
        try {
            $this->phicommRegister($request->phone, $password, $request->verifyCode);
            $phicommId = $this->phicommLogin($request->phone, $password);
        } catch (\Exception $e) {
            return Redirect::back()->withInput(Input::except('password'))->withErrors($e->getMessage());
        }

        return view('phicomm.bind')->withPhicommId($phicommId);
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
        try {
            $phicommId = $phicommToken ? $this->getIdFromToken($phicommToken) : $this->phicommLogin($phone, $password);
        } catch (\Exception $e) {
            return Redirect::back()->withInput(Input::except('password'))->withErrors($e->getMessage());
        }

        $user = User::findUserByPhicommId($phicommId);
        if ($user) {
            if ($user->hasRole('NoLogin')) {
                return Redirect::back()
                    ->withInput(Input::except('password'))
                    ->withError('您已被系统管理员禁止登录');
            }
            Auth::loginUsingId($user->id);
            return Redirect::intended('/')
                ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.login.success')));
        } else {
            return view('phicomm.bind');
        }
    }

    private function phicommLogin($phone, $password)
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

    public function getCreate()
    {
        return view('phicomm.create');
    }

    public function postBind()
    {
        $this->validate(request(), [
            'phicomm_id' => 'required|integer|min:1',
            'username' => 'required',
        ]);
        $userData = [
            'phicomm_id' => request('phicomm_id'),
            'username' => request('username'),
            'password' => str_random(32),
            'regip' => request()->server('REMOTE_ADDR'),
        ];
        $user = User::create($userData);
        event(new UserWasAddedEvent($user));
        Auth::login($user);

        return redirect('/');
    }

    public function forget()
    {
        return view('phicomm.forget');
    }

    public function reset()
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
            'password' => 'required|string|min:6',
            'verifyCode' => 'required|size:6',
        ]);
        $password = strtoupper(md5(request('password')));
        try {
            $this->phicommReset(request('phone'), $password, request('verifyCode'));
        } catch (\Exception $e) {
            return back()->withInput(Input::except('password'))->withErrors($e->getMessage());
        }
        return redirect('/phicomm/login')->withSuccess('密码重置成功');
    }

    private function phicommReset($phone, $password, $verifyCode)
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

    /**
     * 发送短信验证码
     */
    public function sendVerifyCode()
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
        ]);
        $phone = request('phone');
        try {
            $this->checkPhoneAvailable($phone);
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
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => 'VerifyCode Send Successfully'];
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