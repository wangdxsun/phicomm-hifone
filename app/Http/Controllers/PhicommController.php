<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/1/18
 * Time: 20:49
 */

namespace Hifone\Http\Controllers;

use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Http\Bll\PhicommBll;
use Hifone\Models\Provider;
use Hifone\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Config;
use Session;
use Redirect;
use Auth;
use Input;

class PhicommController extends Controller
{
    use ThrottlesLogins, AuthenticatesAndRegistersUsers;

    private $phicomm;

    public function __construct(PhicommBll $phicomm)
    {
        $this->middleware('guest', ['except' => ['logout', 'getLogout']]);
        $this->phicomm = $phicomm;

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
            $this->phicomm->checkPhoneAvailable($request->phone);
            $this->phicomm->register($request->phone, $password, $request->verifyCode);
            $phicommId = $this->phicomm->login($request->phone, $password);
        } catch (\Exception $e) {
            return Redirect::back()->withInput(Input::except('password'))->withErrors($e->getMessage());
        }

        return view('phicomm.bind')->withPhicommId($phicommId);
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
            'phone' => 'required|phone',
            'password' => 'required',
        ]);
        $phone = $request->get('phone');
        $password = strtoupper(md5($request->get('password')));
        try {
            $phicommId = $this->phicomm->login($phone, $password);
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

    public function getCreate()
    {
        return view('phicomm.create');
    }

    public function postBind(PhicommBll $phicommBll)
    {
        $this->validate(request(), [
            'phicomm_id' => 'required|integer|min:1',
            'username' => 'required',
        ]);
        try {
            $phicommBll->bind();
        } catch (\Exception $e) {
            return back()->withInput()->withErrors($e->getMessage());
        }

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
            'verify' => 'required|size:6',
        ]);
        $password = strtoupper(md5(request('password')));
        try {
            $this->phicomm->reset(request('phone'), $password, request('verify'));
        } catch (\Exception $e) {
            return back()->withInput(Input::except('password'))->withErrors($e->getMessage());
        }
        return redirect('/phicomm/login')->withSuccess('密码重置成功');
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
            $this->phicomm->sendVerifyCode($phone);
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => 'VerifyCode Send Successfully'];
    }
}