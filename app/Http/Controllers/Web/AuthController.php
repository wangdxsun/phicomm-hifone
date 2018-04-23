<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Web;

use Hifone\Commands\Identity\AddIdentityCommand;
use Hifone\Exceptions\HifoneException;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Redis;
use Session;
use Input;

class AuthController extends WebController
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    public function __construct()
    {
        $this->middleware('guest', ['except' => ['login', 'logout']]);
    }

    public function login()
    {

        //3分钟内密码连续输错5次账号锁定15分钟
        $username = request('username');
        $ip = getClientIp();
        $redisKey = $username . "|" . $ip;
        if (Redis::get($redisKey) >= 5) {
            $second = Redis::ttl($redisKey);
            throw new HifoneException('该账号已被锁定，请'. intval($second / 60) . '分'. $second % 60 .'秒后再试');
        }

        $this->validate(request(), [
            'username' => 'required',
            'password' => 'required',
            'captcha' => 'required',//图形验证码必填
        ]);
        // Login with username only.
        $loginData = Input::only(['username', 'password']);

        $captcha = request('captcha');
        if ($captcha != Session::get('phrase')) {
            // instructions if user phrase is good
            throw new HifoneException('验证码有误');
        }

        // Validate login credentials.
        if (Auth::validate($loginData)) {
            // 登录并且「记住」用户
            Auth::attempt($loginData, request()->has('remember'));

            if (Session::has('connect_data')) {
                $connect_data = Session::get('connect_data');
                dispatch(new AddIdentityCommand(Auth::user()->id, $connect_data));
            }

            if (Auth::user()->hasRole('NoLogin')) {
                Auth::logout();
                throw new HifoneException('您已被系统管理员禁止登录');
            }
            
            return Auth::user();
        } else {
            //使用缓存实现3分钟内密码连续输错5次账号锁定15分钟,以login|ip作为标识
            if (Redis::incr($redisKey) >= 5) {
                $locked = env('AUTH_LOGIN_LOCKED') ? : 15;
                Redis::expire($redisKey, 60 * $locked);
                throw new HifoneException('该账号已被锁定，请15分钟后再试');
            } else {
                $expire = env('AUTH_LOGIN_EXPIRE') ? : 3;//三分钟内连续输错
                Redis::expire($redisKey, 60 * $expire);
                if (Redis::get($redisKey) >= 3) {
                    throw new HifoneException('您还有'. (5 - Redis::get($redisKey)) . '次密码输入机会' );
                } else {
                    throw new HifoneException('用户名或密码错误，请重新输入');
                }
            }
        }
    }

}
