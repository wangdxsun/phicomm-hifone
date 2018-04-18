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
use Hifone\Models\Permission;
use Hifone\Models\Role;
use Redirect;
use View;
use Input;

class UserGroupController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share([
            'page_title'     => '用户组管理',
            'current_menu'   => 'user',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $roles = Role::userGroup()->with(['permissions', 'user'])->get();

        return View::make('dashboard.groups.users.index')
            ->withRoles($roles);
    }

    /**
     * Shows the create user view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $permissions = Permission::userGroup()->get();

        return View::make('dashboard.groups.users.create_edit')
            ->withPermissions($permissions)
            ->withSubHeader('新增用户组');
    }

    /**
     * Stores a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        if (!\Auth::user()->can('user_group')) {
            return \Redirect::back()->withErrors('您没有添加用户组的权限');
        }
        $roleData = Input::get('role');
        $roleData['user_id'] = \Auth::user()->id;
        $permissions = Input::get('permissions', []);
        dd($permissions);
        try {
            \DB::transaction(function () use ($roleData, $permissions) {
                $role = Role::create($roleData);
                $role->permissions()->attach($permissions);
            });
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withInput($roleData)
                ->withTitle('用户组添加失败')
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.group.users.index')->withSuccess('用户组添加成功');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::userGroup()->get();
        return View::make('dashboard.groups.users.create_edit')
            ->withRole($role)
            ->withPermissions($permissions)
            ->withSubHeader('修改用户组');
    }

    public function update(Role $role)
    {
        if (!\Auth::user()->can('user_group')) {
            return Redirect::back()->withErrors('您没有修改管理组的权限');
        }
        $roleData = Input::get('role');
        $permissions = Input::get('batch', []);
        try {
            \DB::transaction(function () use ($role, $roleData, $permissions) {
                $role->update($roleData);
                $role->permissions()->sync($permissions);
            });
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withInput($roleData)
                ->withTitle('用户组修改失败')
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.group.users.index')->withSuccess('用户组修改成功');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return Redirect::back()->withErrors('无法删除该用户组');
        }
        $role->delete();
        return Redirect::back()->withSuccess('删除成功');
    }
}
