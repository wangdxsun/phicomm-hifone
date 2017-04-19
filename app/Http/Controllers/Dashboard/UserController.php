<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Dashboard;

use AltThree\Validator\ValidationException;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Role;
use Hifone\Models\User;
use Redirect;
use View;
use Input;

class UserController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share([
            'current_menu'  => 'users',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $search = array_filter(Input::get('user', []), function($value) {
            return !empty($value);
        });
        $users = User::search($search)->with('roles', 'lastOpUser')->paginate(20);
        $roles = Role::all();

        return View::make('dashboard.users.index')
            ->withPageTitle(trans('dashboard.users.users').' - '.trans('dashboard.dashboard'))
            ->withUsers($users)
            ->withRoles($roles)
            ->withAllUsers(User::all());
    }

    /**
     * Shows the create user view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::adminGroup()->get();
        return View::make('dashboard.users.create_edit')
            ->withRoles($roles)
            ->withPageTitle(trans('dashboard.users.add.title').' - '.trans('dashboard.dashboard'));
    }

    /**
     * Stores a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $userData = Input::get('user');
        $roleId = Input::get('roleId');

        try {
            \DB::transaction(function () use ($userData, $roleId) {
                $user = User::create($userData);
                $user->role_id = $roleId;
                $this->updateOpLog($user, '创建用户');
            });
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.user.create_edit')
                ->withInput($userData)
                ->withTitle('用户添加失败')
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.user.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.users.add.success')));
    }

    public function edit(User $user)
    {
        $roles = Role::adminGroup()->get();

        return View::make('dashboard.users.create_edit')
            ->withPageTitle(trans('dashboard.users.add.title').' - '.trans('dashboard.dashboard'))
            ->withUser($user)
            ->withRoles($roles);
    }

    public function update(User $user)
    {
        $userData = Input::get('user');
        $roleId = Input::get('roleId');
        if (array_get($userData, 'password')) {
            $userData['salt'] = str_random(6);
            $userData['password'] = $this->hasher->make($userData['password'], ['salt' => $userData['salt']]);
        }
        try {
            \DB::transaction(function () use ($user, $userData, $roleId) {
                $user->update($userData);
                $user->role_id = $roleId;
                $this->updateOpLog($user, '修改用户信息');
            });
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withInput(Input::except('password'))
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), '用户修改失败'))
                ->withErrors($e->getMessageBag());
        }
        return Redirect::back()
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.users.edit.success')));
    }

    //恢复默认头像
    public function avatar(User $user)
    {
        $user->avatar_url = '';
        $this->updateOpLog($user, '恢复默认头像');

        return Redirect::back()->withSuccess('头像删除成功');
    }

    //禁言或取消禁言
    public function comment(User $user)
    {
        $user->role_id = ($user->role_id == Role::NO_COMMENT) ? Role::REGISTER_USER : Role::NO_COMMENT;
        $this->updateOpLog($user, '');
        return Redirect::back()->withSuccess('修改成功');
    }

    //禁止登录或者取消登录
    public function login(User $user)
    {
        $user->role_id = ($user->role_id == Role::NO_LOGIN) ? Role::REGISTER_USER : Role::NO_LOGIN;

        return Redirect::back()->withSuccess('修改成功');
    }
}
