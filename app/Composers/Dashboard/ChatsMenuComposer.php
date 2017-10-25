<?php
namespace Hifone\Composers\Dashboard;

use Illuminate\Contracts\View\View;

class ChatsMenuComposer
{
    public function compose(View $view)
    {
        $subMenu = [
            'send' => [
            'title'  => trans('dashboard.chat.send'),
            'url'    => route('dashboard.chat.send'),
            'icon'   => 'fa fa-envelope-o',
            'active' => false,
            ],
            'lists' => [
            'title'  => trans('dashboard.chat.lists'),
            'url'    => route('dashboard.chat.lists'),
            'icon'   => 'fa fa-clock-o',
            'active' => false,
            ],
        ];

        $view->withSubMenu($subMenu);
        $view->withSubTitle(trans('dashboard.chat.send'));
    }
}