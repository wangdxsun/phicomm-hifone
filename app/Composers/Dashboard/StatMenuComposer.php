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

class StatMenuComposer
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
            'index' => [
                'title'  => '总体趋势',
                'url'    => route('dashboard.stat.index'),
                'icon'   => 'fa fa-bar-chart',
                'active' => false,
            ],
            'banner' => [
                'title'  => 'banner',
                'url'    => route('dashboard.stat.banner'),
                'icon'   => 'fa fa-image',
                'active' => false,
            ],
            'node' => [
                'title'  => '板块统计',
                'url'    => route('dashboard.stat.node'),
                'icon'   => 'fa fa-sitemap',
                'active' => false,
            ],
        ];

        $view->withSubMenu($subMenu);
        $view->withSubTitle('数据统计');
    }
}
