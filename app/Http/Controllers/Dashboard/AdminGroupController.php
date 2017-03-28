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
use Hifone\Events\Role\RoleWasRemovedEvent;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Permission;
use Hifone\Models\Role;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
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
        $roles = Role::adminGroup()->get();

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
            ->withPermissions($permissions);
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
        $permissions = Permission::adminGroup()->get();
        return View::make('dashboard.groups.admins.create_edit')
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
        if ($role->users()->count() > 0) {
            Redirect::back()->withErrors('无法删除该管理组');
        }
        event(new RoleWasRemovedEvent($role));
        $role->delete();
        \Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}
