<?php
namespace Hifone\Composers\Dashboard;
use Illuminate\Contracts\View\View;

class TagMenuComposer
{
    public function compose(View $view)
    {
        $subMenu = [
            'userTagType' => [
                'title'  => '标签分类',
                'url'    => route('dashboard.tag.type.index'),
                'icon'   => 'fa fa-tags',
                'active' => false,
            ],
            'userTag' => [
                'title'  => '用户标签',
                'url'    => route('dashboard.tag'),
                'icon'   => 'fa fa-user',
                'active' => false,
            ],
        ];
        $view->withSubMenu($subMenu);
        $view->withSubTitle('标签管理');
    }
}