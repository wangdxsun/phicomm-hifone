<?php
namespace Hifone\Composers\Dashboard;

use Illuminate\Contracts\View\View;

class QuestionMenuComposer
{
    public function compose(View $view)
    {
        $subMenu = [
            'question' => [
                'title'  => '提问',
                'url'    => route('dashboard.questions.audit'),
                'icon'   => 'fa fa-check',
                'active' => false,
                'sub_nav'=> [
                    'audit' => [
                        'title'  => '待审核',
                        'url'    => route('dashboard.questions.audit'),
                        'icon'   => 'fa fa-eye',
                        'active' => false,
                    ],
                    'index' => [
                        'title'  => '审核通过',
                        'url'    => route('dashboard.questions.index'),
                        'icon'   => 'fa fa-check',
                        'active' => false,
                    ],
                    'trash' => [
                        'title'  => '回收站',
                        'url'    => route('dashboard.questions.trash'),
                        'icon'   => 'fa fa-trash',
                        'active' => false,
                    ],
                ],
            ],
            'answer' => [
                'title'  => '回答',
                'url'    => route('dashboard.answers.audit'),
                'icon'   => 'fa fa-bar-chart',
                'active' => false,
                'sub_nav'=> [
                    'audit' => [
                        'title'  => '待审核',
                        'url'    => route('dashboard.answers.audit'),
                        'icon'   => 'fa fa-eye',
                        'active' => false,
                    ],
                    'index' => [
                        'title'  => '审核通过',
                        'url'    => route('dashboard.answers.index'),
                        'icon'   => 'fa fa-check',
                        'active' => false,
                    ],
                    'trash' => [
                        'title'  => '回收站',
                        'url'    => route('dashboard.answers.trash'),
                        'icon'   => 'fa fa-trash',
                        'active' => false,
                    ],
                ],
            ],
            'comment' => [
                'title'  => '回复',
                'url'    => route('dashboard.comments.audit'),
                'icon'   => 'fa fa-bar-chart',
                'active' => false,
                'sub_nav'=> [
                    'audit' => [
                        'title'  => '待审核',
                        'url'    => route('dashboard.comments.audit'),
                        'icon'   => 'fa fa-eye',
                        'active' => false,
                    ],
                    'index' => [
                        'title'  => '审核通过',
                        'url'    => route('dashboard.comments.index'),
                        'icon'   => 'fa fa-check',
                        'active' => false,
                    ],
                    'trash' => [
                        'title'  => '回收站',
                        'url'    => route('dashboard.comments.trash'),
                        'icon'   => 'fa fa-trash',
                        'active' => false,
                    ],
                ],

            ],

        ];

        $view->withSubMenu($subMenu);
        $view->withSubTitle('问答管理');
    }
}