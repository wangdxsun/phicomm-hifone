<?php

namespace Hifone\Composers\Dashboard;
use Illuminate\Contracts\View\View;

class QuestionTagMenuComposer
{
    public function compose(View $view)
    {
        $subMenu = [
            'questionTagType' => [
                'title'  => '问题分类',
                'url'    => route('dashboard.question.tag.type'),
                'icon'   => 'fa fa-sitemap',
                'active' => false,
            ],
            'questionTag' => [
                'title'  => '问题子类',
                'url'    => route('dashboard.question.tag'),
                'icon'   => 'fa fa-bookmark',
                'active' => false,
            ],
        ];
        $view->withSubMenu($subMenu);
        $view->withSubTitle('问题分类管理');
    }
}
