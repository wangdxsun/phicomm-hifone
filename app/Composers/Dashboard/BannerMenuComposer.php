<?php
namespace Hifone\Composers\Dashboard;

use Illuminate\Contracts\View\View;

class BannerMenuComposer
{
    public function compose(View $view)
    {
        $subMenu = [
            'index' => [
                'title'  => trans('dashboard.banner.show'),
                'url'    => route('dashboard.carousel.index'),
                'icon'   => 'fa fa-envelope-o',
                'active' => false,
            ],
            'hide' => [
                'title'  => trans('dashboard.banner.hide'),
                'url'    => route('dashboard.carousel.hide'),
                'icon'   => 'fa fa-clock-o',
                'active' => false,
            ],
        ];

        $view->withSubMenu($subMenu);
    }
}