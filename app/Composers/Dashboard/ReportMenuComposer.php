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

class ReportMenuComposer
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
            'audit' => [
                'title'  => '待处理',
                'url'    => route('dashboard.report.audit'),
                'icon'   => 'fa fa-eye',
                'active' => false,
            ],
            'index' => [
                'title'  => '处理日志',
                'url'    => route('dashboard.report.index'),
                'icon'   => 'fa fa-check',
                'active' => false,
            ],
        ];

        $view->withSubMenu($subMenu);
    }
}
