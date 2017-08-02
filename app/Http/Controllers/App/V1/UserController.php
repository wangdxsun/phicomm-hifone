<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/1
 * Time: 15:08
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\PhicommBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\User;

class UserController extends AppController
{
    public function bind(PhicommBll $phicommBll)
    {
        $this->validate(request(), [
            'username' => 'required|max:15|regex:/\A[\x{4e00}-\x{9fa5}A-Za-z0-9\-\_\.]+\z/u',
        ], [
            'username.regex' => '用户名含有非法字符'
        ]);
        $phicommBll->bind();

        return success('创建成功');
    }

    public function show()
    {
        $user = User::findUserByPhicommId(\Auth::phicommId());
        if (!$user) {
            throw new \Exception('用户不存在');
        }
        return ['username' => $user->username];
    }
}