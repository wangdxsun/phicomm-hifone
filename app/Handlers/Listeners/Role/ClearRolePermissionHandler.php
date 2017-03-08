<?php
/**
 * Created by PhpStorm.
 * User: jql
 * Date: 07/03/2017
 * Time: 8:14 PM
 */

namespace Hifone\Handlers\Listeners\Role;

use Hifone\Events\Role\RoleWasRemovedEvent;

class ClearRolePermissionHandler
{
    public function handler(RoleWasRemovedEvent $event)
    {
        $permissions = $event->role->permissions();
        foreach ($permissions as $permission) {
            $permission->delete();
        }
        //$event->role->permissions()->delete();

    }
}