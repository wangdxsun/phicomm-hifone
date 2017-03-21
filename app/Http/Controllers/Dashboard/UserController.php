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
use Hifone\Hashing\PasswordHasher;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Role;
use Hifone\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Input;

class UserController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct(PasswordHasher $hasher)
    {
        $this->hasher = $hasher;

        View::share([
            'current_menu'  => 'nodes',
            'sub_title'     => trans_choice('dashboard.nodes.nodes', 2),
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
        $users = User::search($search)->orderBy('created_at', 'desc')->paginate(20);
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
        $roles = Role::all();
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
        $roles = Input::get('roles');

        try {
            \DB::transaction(function () use ($userData, $roles) {
                $user = User::create($userData);
                $user->roles()->attach($roles);
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
        $roles = Role::all();
        $this->subMenu['users']['active'] = true;

        return View::make('dashboard.users.create_edit')
            ->withPageTitle(trans('dashboard.users.add.title').' - '.trans('dashboard.dashboard'))
            ->withUser($user)
            ->withRoles($roles)
            ->withSubMenu($this->subMenu);
    }

    public function update(User $user)
    {
        $userData = Input::get('user');
        $roles = Input::get('roles');
        if ($userData['password']) {
            $userData['salt'] = str_random(6);
            $userData['password'] = $this->hasher->make($userData['password'], ['salt' => $userData['salt']]);
        } else {
            unset($userData['password']);
        }

        try {
            \DB::transaction(function () use ($user, $userData, $roles) {
                $user->update($userData);
                $user->roles()->sync($roles);
            });
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.user.edit', ['id' => $user->id])
                ->withInput(Input::except('password'))
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), '用户修改失败'))
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.user.edit', ['id' => $user->id])
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.users.edit.success')));
    }
}
