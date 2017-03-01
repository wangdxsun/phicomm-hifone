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

class ReplyMenuComposer
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
                'title'  => '待审核',
                'url'    => route('dashboard.reply.audit'),
                'icon'   => 'fa fa-eye',
                'active' => false,
            ],
            'index' => [
                'title'  => '审核通过',
                'url'    => route('dashboard.reply.index'),
                'icon'   => 'fa fa-check',
                'active' => false,
            ],
            'trash' => [
                'title'  => '回收站',
                'url'    => route('dashboard.reply.trash'),
                'icon'   => 'fa fa-trash',
                'active' => false,
            ],
        ];

        $view->withSubMenu($subMenu);
        $view->withSubTitle(trans('dashboard.content.content'));
    }
}
