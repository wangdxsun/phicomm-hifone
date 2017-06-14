<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/1/18
 * Time: 20:49
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\PhicommBll;
use Hifone\Models\User;
use Illuminate\Http\Request;
use Auth;
use Session;

class PhicommController extends ApiController
{
    private $phicommBll;

    public function __construct(PhicommBll $phicommBll)
    {
        $this->phicommBll = $phicommBll;
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

        return success('云账号注册成功');
    }

    public function login()
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
            'password' => 'required',
        ]);
        $phone = request('phone');
        $password = strtoupper(md5(request('password')));
        $phicommId = $this->phicommBll->login($phone, $password);

        $user = User::findUserByPhicommId($phicommId);
        if ($user) {
            if ($user->hasRole('NoLogin')) {
                return response('对不起，你已被管理员禁止登录', 403);
            }
            Auth::login($user);
            $cloudUser = $this->phicommBll->userInfo();
            if ($cloudUser['img'] && $user->avatar_url != $cloudUser['img']) {
                $user->avatar_url = $cloudUser['img'];
                $user->save();
            }
            return $user;
        } else {
            Session::set('phicommId', $phicommId);
            return 'Unbind';
        }
    }

    public function bind(PhicommBll $phicommBll)
    {
        $this->validate(request(), [
            'username' => 'required',
        ]);
        $user = $phicommBll->bind();

        return $user;
    }

    public function reset()
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
            'password' => 'required|string|min:6',
            'verify' => 'required|size:6',
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
        } elseif (request('type') == 'reset' && $this->phicommBll->checkPhoneAvailable(request('phone'))) {
            throw new \Exception('该手机号还没有注册');
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