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

class AdminGroupController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share([
            'page_title'     => '管理组',
            'current_menu'   => 'admin',
        ]);
    }

    /**
     * Shows the users view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $roles = Role::adminGroup()->with(['permissions', 'user'])->get();

        return View::make('dashboard.groups.admins.index')
            ->withRoles($roles);
    }

    /**
     * Shows the create user view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $permissions = Permission::adminGroup()->get();

        return View::make('dashboard.groups.admins.create_edit')
            ->withPermissions($permissions)
            ->withSubHeader('新增管理组');
    }

    /**
     * Stores a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        if (!\Auth::user()->can('user_group')) {
            return Redirect::back()->withErrors('您没有添加管理组的权限');
        }
        $roleData = Input::get('role');
        $roleData['user_id'] = \Auth::user()->id;
        $permissions = Input::get('permissions');
        try {
            \DB::transaction(function () use ($roleData, $permissions) {
                $role = Role::create($roleData);
                $role->permissions()->attach($permissions);
            });
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withInput($roleData)
                ->withTitle('管理组添加失败')
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.group.admin.index')->withSuccess('管理组添加成功');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::adminGroup()->get();
        return View::make('dashboard.groups.admins.create_edit')
            ->withRole($role)
            ->withPermissions($permissions)
            ->withSubHeader('修改管理组');
    }

    public function update(Role $role)
    {
        if (!\Auth::user()->can('user_group')) {
            return \Redirect::back()->withErrors('您没有修改管理组的权限');
        }
        $roleData = Input::get('role');
        $permissions = Input::get('permissions');
        try {
            \DB::transaction(function () use ($role, $roleData, $permissions) {
                $role->update($roleData);
                $role->permissions()->sync($permissions);
            });
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withInput($roleData)
                ->withTitle('管理组修改失败')
                ->withErrors($e->getMessageBag());
        }
        return Redirect::route('dashboard.group.admin.index')->withSuccess('管理组修改成功');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return Redirect::back()->withErrors('该组有用户存在，不可删除');
        }
        $role->delete();
        return Redirect::back()->withSuccess('删除成功');
    }
}
