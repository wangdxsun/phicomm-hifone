<?php
namespace Hifone\Composers\Dashboard;

use Illuminate\Contracts\View\View;

class ReportMenuComposer
{
    public function compose(View $view)
    {
        $sub_menu = [
            //提问和回答
            'question' => [
                'title'  => '提问和回答',
                'url'    => route('dashboard.reports.question'),
                'active' => false,
            ],
            //帖子和回帖
            'thread' => [
                'title'  => '帖子和回帖',
                'url'    => route('dashboard.reports.thread'),
                'active' => false,
            ],
        ];
        $view->with('sub_menu', $sub_menu);
        $view->with('sub_title', '举报管理');
    }
}