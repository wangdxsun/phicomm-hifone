<?php
namespace Hifone\Composers\Dashboard;
use Illuminate\Contracts\View\View;

class TagMenuComposer
{
    public function compose(View $view)
    {
        $subMenu = [
            'index' => [
                'title'  => '标签分类',
                'url'    => route('dashboard.tag.type.index'),
                'icon'   => 'fa fa-bar-chart',
                'active' => false,
            ],
            'tag' => [
                'title'  => '标签',
                'url'    => route('dashboard.tag.user'),
                'icon'   => 'fa fa-image',
                'active' => false,
            ],
        ];

        $subNav = [
            'user'   => [
                'title'  => '用户标签',
                'url'    => route('dashboard.tag.user'),
                'active' => false,
                'icon'   => 'fa fa-user',
            ],
        ];

        $view->withSubMenu($subMenu);
        $view->withSubNav($subNav);
        $view->withSubTitle('标签管理');
    }
}