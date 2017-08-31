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

class NodeMenuComposer
{

    public function compose(View $view)
    {
        $subMenu = [
            'sections' => [
                'title'  => trans('dashboard.sections.sections'),
                'url'    => route('dashboard.section.index'),
                'icon'   => 'fa fa-folder',
                'active' => false,
            ],
            'nodes' => [
                'title'  => trans('dashboard.nodes.nodes'),
                'url'    => route('dashboard.node.index'),
                'icon'   => 'fa fa-sitemap',
                'active' => false,
            ],
            'subNodes' => [
                'title'  => trans('dashboard.nodes.sub_nodes'),
                'url'    => route('dashboard.subNode.index'),
                'icon'   => 'fa fa-sitemap',
                'active' => false,
            ],
        ];

        $view->withSubMenu($subMenu);
        $view->withSubTitle(trans_choice('dashboard.nodes.nodes', 2));
    }
}
