<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/1/18
 * Time: 20:49
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Exceptions\HifoneException;
use Hifone\Http\Bll\CommonBll;
use Hifone\Http\Bll\PhicommBll;
use Hifone\Models\User;
use Hifone\Services\Filter\WordsFilter;
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
        $res = $this->phicommBll->login($request->phone, $password);
        $phicommId = $res['uid'];
        Session::set('phicommId', $phicommId);

        return response(['user' => 'Unbind']);
    }

    public function login(CommonBll $commonBll)
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
            'password' => 'required',
        ]);
        $phone = request('phone');
        $password = strtoupper(md5(request('password')));
        $res = $this->phicommBll->login($phone, $password);
        $phicommId = $res['uid'];

        $user = User::findUserByPhicommId($phicommId);
        if ($user) {
            if ($user->hasRole('NoLogin')) {
                return response('对不起，你已被管理员禁止登录', 403);
            }
            Auth::login($user);
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
                $this->phicommBll->checkPhoneRegistered(request('phone'));
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