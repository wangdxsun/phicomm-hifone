<?php
namespace Hifone\Composers\Dashboard;
use Illuminate\Contracts\View\View;

class TagMenuComposer
{
    public function compose(View $view)
    {
        $subMenu = [
            'userTag' => [
                'title'  => '用户标签',
                'url'    => route('dashboard.tag.user'),
                'icon'   => 'fa fa-user',
                'active' => false,
            ],
            'questionTag' => [
                'title'  => '问题标签',
                'url'    => route('dashboard.tag.question'),
                'icon'   => 'fa fa-question',
                'active' => false,
            ],
        ];
        $view->withSubMenu($subMenu);
        $view->withSubTitle('标签管理');
    }
}