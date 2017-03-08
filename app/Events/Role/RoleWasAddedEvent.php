<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Role;

use Hifone\Models\Role;

final class RoleWasAddedEvent implements RoleEventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Role
     */
    public $role;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }
}
