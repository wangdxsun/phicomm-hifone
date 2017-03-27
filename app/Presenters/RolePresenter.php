<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Presenters;

use Hifone\Presenters\Traits\TimestampsTrait;

class RolePresenter extends AbstractPresenter
{
    use TimestampsTrait;

    public function hasPermission($permission)
    {
        foreach ($this->wrappedObject->permissions as $value) {
            if ($value->name == $permission->name) {
                return true;
            }
        }
        return false;
    }

    public function permissions()
    {
        return implode('，', array_column($this->wrappedObject->permissions->toArray(), 'display_name'));
    }

    public function toArray()
    {
        return array_merge($this->wrappedObject->toArray(), [
            'created_at' => $this->created_at(),
            'updated_at' => $this->updated_at(),
        ]);
    }
}
