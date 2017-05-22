<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/1/18
 * Time: 20:49
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Http\Bll\PhicommBll;
use Hifone\Models\User;
use Illuminate\Http\Request;
use Auth;
use Response;

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
            'verifyCode' => 'required',
        ]);
        $password = strtoupper(md5($request->get('password')));
        $this->phicommBll->checkPhoneAvailable($request->phone);
        $this->phicommBll->register($request->phone, $password, $request->verifyCode);
        $phicommId = $this->phicommBll->login($request->phone, $password);

        return compact($phicommId);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'phicommToken' => 'required_without:phone',
            'phone' => 'required_without:phicommToken|phone',
            'password' => 'required_with:phone',
        ]);
        $phicommToken = $request->get('phicommToken');
        $phone = $request->get('phone');
        $password = strtoupper(md5($request->get('password')));
        $phicommId = $phicommToken ? $this->phicommBll->getIdFromToken($phicommToken) : $this->phicommBll->login($phone, $password);

        $user = User::findUserByPhicommId($phicommId);
        if ($user) {
            if ($user->hasRole('NoLogin')) {
                return response('您已被系统管理员禁止登录', 401);
            }
            Auth::loginUsingId($user->id);
            return response(['bind' => true]);
        } else {
            return response(['bind' => false]);
        }
    }

    public function bind(PhicommBll $phicommBll)
    {
        $this->validate(request(), [
            'username' => 'required',
        ]);
        $phicommBll->bind();

        return response()->json('绑定成功');
    }

    public function reset()
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
            'password' => 'required|string|min:6',
            'verifyCode' => 'required|size:6',
        ]);
        $password = strtoupper(md5(request('password')));
        $this->phicommBll->reset(request('phone'), $password, request('verifyCode'));

        return Response::json('密码重置成功');
    }

    /**
     * 发送短信验证码
     */
    public function verify()
    {
        $this->validate(request(), [
            'phone' => 'required|phone',
        ]);
        $phone = request('phone');
        $this->phicommBll->sendVerifyCode($phone);

        return Response::json('验证码发送成功');
    }
}