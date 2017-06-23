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

use Hifone\Composers\AppComposer;
use Hifone\Composers\CurrentUserComposer;
use Hifone\Composers\Dashboard\AdvertisementMenuComposer;
use Hifone\Composers\Dashboard\ContentMenuComposer;
use Hifone\Composers\Dashboard\NodeMenuComposer;
use Hifone\Composers\Dashboard\ReplyMenuComposer;
use Hifone\Composers\Dashboard\RoleMenuComposer;
use Hifone\Composers\Dashboard\SettingMenuComposer;
use Hifone\Composers\Dashboard\StatMenuComposer;
use Hifone\Composers\Dashboard\ThreadMenuComposer;
use Hifone\Composers\Dashboard\UserMenuComposer;
use Hifone\Composers\LocaleComposer;
use Hifone\Composers\SidebarComposer;
use Hifone\Composers\TimezoneComposer;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;

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
        $factory->composer('*', AppComposer::class);
        $factory->composer('*', CurrentUserComposer::class);
        $factory->composer('partials.sidebar', SidebarComposer::class);
        $factory->composer(['dashboard.settings.*', 'users.edit'], LocaleComposer::class);
        $factory->composer(['install.*', ], TimezoneComposer::class);
        $factory->composer(['dashboard.adblocks.*', 'dashboard.advertisements.*', 'dashboard.adspaces.*', ], AdvertisementMenuComposer::class);
        $factory->composer(['dashboard.threads.*',], ThreadMenuComposer::class);
        $factory->composer(['dashboard.replies.*',], ReplyMenuComposer::class);
        $factory->composer(['dashboard.photos.*', 'dashboard.pages.*', ], ContentMenuComposer::class);
        $factory->composer(['dashboard.nodes.*', 'dashboard.sections.*'], NodeMenuComposer::class);
        $factory->composer(['dashboard.groups.*'], RoleMenuComposer::class);
        $factory->composer(['dashboard.stats.*'], StatMenuComposer::class);
        $factory->composer(['dashboard.tips.*', 'dashboard.links.*', 'dashboard.locations.*', 'dashboard.settings.*', ], SettingMenuComposer::class);
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
