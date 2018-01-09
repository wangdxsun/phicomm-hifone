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
                'title'  => '版块统计',
                'url'    => route('dashboard.stat.node'),
                'icon'   => 'fa fa-sitemap',
                'active' => false,
            ],
            'user' => [
                'title'  => '用户统计',
                'url'    => route('dashboard.stat.user'),
                'icon'   => 'fa fa-user',
                'active' => false,
            ],
            'thread' => [
                'title'  => '新增发帖',
                'url'    => route('dashboard.stat.daily.threads.count'),
                'icon'   => 'fa fa-file',
                'active' => false,
            ],
            'reply' => [
                'title'  => '新增回帖',
                'url'    => route('dashboard.stat.daily.replies.count'),
                'icon'   => 'fa fa-file',
                'active' => false,
            ],
            'zeroReply' => [
                'title'  => '零回复统计',
                'url'    => route('dashboard.stat.zeroReply'),
                'icon'   => 'fa fa-file',
                'active' => false,
            ],
            'userInteraction' => [
                'title'  => '用户互动',
                'url'    => route('dashboard.stat.interaction'),
                'icon'   => 'fa fa-user',
                'active' => false,
            ],
        ];
        $subNav = [
            'basic' => [
                'title'  => '用户基本情况',
                'url'    => route('dashboard.stat.user'),
                'active' => false,
                'src'    => 'basic'
            ],
            'app'   => [
                'title'  => 'App活跃用户',
                'url'    => route('dashboard.stat.user.app'),
                'active' => false,
                'src'    => 'app'
            ],
            'web'   => [
                'title'  => 'WEB活跃用户',
                'url'    => route('dashboard.stat.user.web'),
                'active' => false,
                'src'    => 'web'
            ],
            'H5'    => [
                'title'  => 'H5活跃用户',
                'url'    => route('dashboard.stat.user.h5'),
                'active' => false,
                'src'    => 'h5'
            ]
        ];
        $view->withSubMenu($subMenu);
        $view->withSubNav($subNav);
        $view->withSubTitle('数据统计');
    }
}
