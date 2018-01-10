<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\PhicommBll;
use Hifone\Models\User;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Http\Request;
use Auth;
use Session;
use Config;
use Redirect;
use Input;

class PhicommController extends WebController
{
    private $phicommBll;

    public function __construct(PhicommBll $phicommBll)
    {
        $this->phicommBll = $phicommBll;
    }

    public function preRegister(Request $request)
    {
        //验证当前手机号是否可注册，并验证图形验证码
        $this->validate($request, [
            'phone' => 'required|phone',
            'captcha' => 'required',
        ]);
        $this->phicommBll->checkPhoneAvailable($request->phone);
        $captcha = request('captcha');
        if (!Config::get('setting.captcha_login_disabled') && $captcha != Session::get('phrase')) {
            // instructions if user phrase is good
            return 'Captcha wrong';
        }

        return 'Success';

    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|phone',
            'password' => 'required',
            'verify' => 'required',
        ]);
        $password = strtoupper(md5($request->get('password')));
        $this->phicommBll->checkPhoneAvailable($request->phone);
        $this->phicommBll->register($request->phone, $password, $request->verify);
        $phicommId = $this->phicommBll->login($request->phone, $password);
        Session::set('phicommId', $phicommId);

        return response(['user' => 'Unbind']);
    }

    public function login(CommonBll $commonBll)
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
            'password' => 'required',
            'captcha' => 'required',//图形验证码必填
        ]);
        $phone = request('phone');
        $password = strtoupper(md5(request('password')));
        $captcha = request('captcha');
        if (!Config::get('setting.captcha_login_disabled') && $captcha != Session::get('phrase')) {
            // instructions if user phrase is good
            return 'Captcha wrong';
        }

        $phicommId = $this->phicommBll->login($phone, $password);

        $user = User::findUserByPhicommId($phicommId);

        if ($user) {
            if ($user->hasRole('NoLogin')) {
                return response('对不起，你已被管理员禁止登录', 403);
            }
            // 登录并且「记住」用户
            Auth::login($user, request()->has('remember'));
            $commonBll->login();
            return $user;
        } else {
            return 'Unbind';
        }
    }

    public function bind(PhicommBll $phicommBll, WordsFilter $wordsFilter)
    {
        $this->validate(request(), [
            'username' => 'required|max:15|regex:/\A[\x{4e00}-\x{9fa5}A-Za-z0-9\-\_\.]+\z/u',
        ], [
            'username.regex' => '昵称含有非法字符'
        ]);
        $user = $phicommBll->bind($wordsFilter);

        return $user;
    }

    public function reset()
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
            'password' => 'required|string|min:6',
            'verify' => 'required',
        ]);
        $password = strtoupper(md5(request('password')));
        $this->phicommBll->reset(request('phone'), $password, request('verify'));

        return success('密码重置成功');
    }

    /**
     * 发送短信验证码
     */
    public function verify()
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
        ]);
        if (request('type') == 'register') {
            $this->phicommBll->checkPhoneAvailable(request('phone'));
        } elseif (request('type') == 'reset') {
            try {
                $this->phicommBll->checkPhoneAvailable(request('phone'));
                throw new HifoneException('该手机号还没有注册');
            } catch (\Exception $e) {
                if ($e->getMessage() <> '该手机号已注册！') {
                    throw $e;
                }
            }
        }
        $this->phicommBll->sendVerifyCode(request('phone'));

        return success('验证码发送成功');
    }

    public function logout()
    {
        Auth::logout();

        return success('退出成功');
    }
}