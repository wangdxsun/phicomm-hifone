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
    public function __construct()
    {
        View::share([
            'page_title'     => '角色管理',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function user()
    {
        $roles = Role::user()->get();

        return View::make('dashboard.roles.index')
            ->withCurrentMenu('user')
            ->withRoles($roles);
    }

    public function admin()
    {
        $roles = Role::admin()->get();

        return View::make('dashboard.roles.index')
            ->withCurrentMenu('admin')
            ->withRoles($roles);
    }

    /**
     * Shows the create user view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $permissions = Permission::all();

        return View::make('dashboard.roles.create_edit')
            ->withPageTitle('修改角色 - '.trans('dashboard.dashboard'))
            ->withPermissions($permissions)
            ->withCurrentMenu('admin');
    }

    /**
     * Stores a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $roleData = Input::get('role');
        $permissions = Input::get('permissions');
        try {
            \DB::transaction(function () use ($roleData, $permissions) {
                $role = Role::create($roleData);
                $role->permissions()->attach($permissions);
            });
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.role.create')
                ->withInput($roleData)
                ->withTitle('角色添加失败')
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.role.index')->withSuccess('角色添加成功');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return View::make('dashboard.roles.create_edit')
            ->withPageTitle('修改角色'.' - '.trans('dashboard.dashboard'))
            ->withRole($role)
            ->withPermissions($permissions);
    }

    public function update(Role $role)
    {
        $roleData = Input::get('role');
        $permissions = Input::get('permissions');
        try {
            \DB::transaction(function () use ($role, $roleData, $permissions) {
                $role->update($roleData);
                $role->permissions()->sync($permissions);
            });
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.role.edit')
                ->withInput($roleData)
                ->withTitle('角色修改失败')
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.role.index')->withSuccess('角色修改成功');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        redirect(route('dashboard.role.index'))->withSuccess('角色删除成功');
    }
}
