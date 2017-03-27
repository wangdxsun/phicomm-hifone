<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Composers\Dashboard;

use Illuminate\Contracts\View\View;

class RoleMenuComposer
{
    /**
     * Bind data to the view.
     *
     * @param \Illuminate\Contracts\View\View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        $subMenu = [
            'user' => [
                'title'  => '用户组',
                'url'    => route('dashboard.group.users.index'),
                'icon'   => 'fa fa-user',
                'active' => false,
            ],
            'admin' => [
                'title'  => '管理组',
                'url'    => route('dashboard.group.admin.index'),
                'icon'   => 'fa fa-users',
                'active' => false,
            ],
        ];

        $view->withSubMenu($subMenu);
        $view->withSubTitle('用户组管理');
    }
}
