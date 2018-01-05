<?php
namespace Hifone\Composers\Dashboard;

use Illuminate\Contracts\View\View;

class BannerMenuComposer
{
    public function compose(View $view)
    {
        $subMenu = [
            //安卓、IOS系统页面
            'app' => [
                'title'  => '安卓/IOS系统',
                'url'    => route('dashboard.carousel.app.show'),
                'active' => false,
            ],
            //web、H5端系统页面
            'web' => [
                'title'  => 'WEB/H5',
                'url'    => route('dashboard.carousel.web.show'),
                'active' => false,
            ],
        ];

        $subNav = [
            //APP端正在展现和已经关闭的banner
            'app_show' => [
                'title'  => '正在展现的banner',
                'url'    => route('dashboard.carousel.app.show'),
                'icon'   => 'fa fa-envelope-o',
                'active' => false,
                'src'    => 'app'
            ],
            'app_hide' => [
                'title'  => '已关闭的banner',
                'url'    => route('dashboard.carousel.app.hide'),
                'icon'   => 'fa fa-clock-o',
                'active' => false,
                'src'    => 'app'
            ],
            //web端正在展现和已经关闭的banner
            'web_show' => [
                'title'  => '正在展现的banner',
                'url'    => route('dashboard.carousel.web.show'),
                'icon'   => 'fa fa-envelope-o',
                'active' => false,
                'src'    => 'web'
            ],
            'web_hide' => [
                'title'  => '已关闭的banner',
                'url'    => route('dashboard.carousel.web.hide'),
                'icon'   => 'fa fa-clock-o',
                'active' => false,
                'src'    => 'web'
            ],
        ];

        $view->withSubMenu($subMenu);
        $view->withSubNav($subNav);
        $view->withSubTitle(trans_choice('Banner管理', 1));
    }
}