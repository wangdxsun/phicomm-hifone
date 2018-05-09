<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Providers;

use Hifone\Composers\Dashboard\AnswerMenuComposer;
use Hifone\Composers\Dashboard\BannerMenuComposer;
use Hifone\Composers\Dashboard\NodeMenuComposer;
use Hifone\Composers\Dashboard\QuestionMenuComposer;
use Hifone\Composers\Dashboard\QuestionTagMenuComposer;
use Hifone\Composers\Dashboard\ReplyMenuComposer;
use Hifone\Composers\Dashboard\RoleMenuComposer;
use Hifone\Composers\Dashboard\SettingMenuComposer;
use Hifone\Composers\Dashboard\StatMenuComposer;
use Hifone\Composers\Dashboard\TagMenuComposer;
use Hifone\Composers\Dashboard\ThreadMenuComposer;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use Hifone\Composers\Dashboard\ChatsMenuComposer;

/**
 * This is the config service provider class.
 */
class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @param \Illuminate\Contracts\View\Factory $factory
     */
    public function boot(Factory $factory)
    {
        $factory->composer(['dashboard.threads.*',], ThreadMenuComposer::class);
        $factory->composer(['dashboard.replies.*',], ReplyMenuComposer::class);
        $factory->composer(['dashboard.nodes.*', 'dashboard.sections.*', 'dashboard.subNodes.*'], NodeMenuComposer::class);
        $factory->composer(['dashboard.groups.*'], RoleMenuComposer::class);
        $factory->composer(['dashboard.stats.*'], StatMenuComposer::class);
        $factory->composer(['dashboard.settings.*', ], SettingMenuComposer::class);
        $factory->composer(['dashboard.chat.*'], ChatsMenuComposer::class);
        $factory->composer(['dashboard.carousel.*'], BannerMenuComposer::class);
        $factory->composer(['dashboard.tagTypes.*', 'dashboard.tags.*'],TagMenuComposer::class);
        //问答相关
        $factory->composer(['dashboard.questions.*', 'dashboard.answers.*', 'dashboard.comments.*'], QuestionMenuComposer::class);
        $factory->composer(['dashboard.questionTags.*', 'dashboard.questionTagTypes.*'], QuestionTagMenuComposer::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
