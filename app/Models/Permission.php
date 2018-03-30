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

use Venturecraft\Revisionable\RevisionableTrait;
use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
    use RevisionableTrait;

    const USER = 0;//用户组
    const ADMIN = 1;//管理组

    //用户组的权限列表
    public function scopeUserGroup($query)
    {
        return $query->where('type', static::USER);
    }

    //管理组的权限列表
    public function scopeAdminGroup($query)
    {
        return $query->where('type', static::ADMIN);
    }
}
