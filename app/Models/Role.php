<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Models;

use Cache;
use DB;
use Hifone\Presenters\RolePresenter;
use McCool\LaravelAutoPresenter\HasPresenter;
use Venturecraft\Revisionable\RevisionableTrait;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole implements HasPresenter
{
    use RevisionableTrait;

    const USER = 0;//用户组
    const ADMIN = 1;//管理组
    const NO_LOGIN = 4;//禁止登录
    const NO_COMMENT = 5;//禁止发言
    const REGISTER_USER = null;

    protected $fillable = ['name', 'display_name', 'description', 'type', 'credit_low', 'credit_high', 'user_id'];

    public static function relationArrayWithCache()
    {
        return Cache::remember('all_assigned_roles', $minutes = 60, function () {
            return DB::table('role_user')->get();
        });
    }

    public static function rolesArrayWithCache()
    {
        return Cache::remember('all_roles', $minutes = 60, function () {
            return DB::table('roles')->get();
        });
    }

    //定义角色的权限
    public function permissions()
    {
        return $this::belongsToMany(Permission::class);
    }

    public function Users()
    {
        return $this->belongsToMany(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return RolePresenter::class;
    }

    //所有的用户组
    public function scopeUserGroup($query)
    {
        return $query->where('type', static::USER);
    }

    //所有的管理组
    public function scopeAdminGroup($query)
    {
        return $query->where('type', static::ADMIN);
    }
}
