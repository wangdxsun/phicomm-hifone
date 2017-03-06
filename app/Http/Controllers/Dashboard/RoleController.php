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
use Hifone\Models\Permission;
use Hifone\Models\Role;
use Hifone\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Input;

class RoleController extends Controller
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
            'current_menu'  => 'roles',
            'sub_title'     => '角色管理',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $roles = Role::all();

        return View::make('dashboard.roles.index')
            ->withPageTitle('角色管理')
            ->withRoles($roles);
    }

    /**
     * Shows the create user view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return View::make('dashboard.roles.create_edit')
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
        $userData['salt'] = str_random(16);
        $userData['password'] = $this->hasher->make($userData['password'], ['salt' => $userData['salt']]);

        try {
            User::create($userData);
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.user.create')
                ->withInput(Input::get('user'))
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.users.add.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.user.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.users.add.success')));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return View::make('dashboard.roles.create_edit')
            ->withPageTitle('修改角色'.' - '.trans('dashboard.dashboard'))
            ->withRole($role)
            ->withPermissions($permissions);
    }

    public function update(User $user)
    {
        $userData = Input::get('user');
        if ($userData['password']) {
            $userData['salt'] = str_random(6);
            $userData['password'] = $this->hasher->make($userData['password'], ['salt' => $userData['salt']]);
        } else {
            unset($userData['password']);
        }
        try {
            $user->update($userData);
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.user.edit', ['id' => $user->id])
                ->withInput(Input::except('password'))
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.users.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.user.edit', ['id' => $user->id])
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.users.edit.success')));
    }
}
