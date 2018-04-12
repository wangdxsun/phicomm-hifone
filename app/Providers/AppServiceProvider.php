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

use Carbon\Carbon;
use Collective\Bus\Dispatcher;
use Hifone\Pipes\UseDatabaseTransactions;
use Hifone\Services\Dates\DateFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @param \Collective\Bus\Dispatcher $dispatcher
     */
    public function boot(Dispatcher $dispatcher)
    {
        $dispatcher->mapUsing(function ($command) {
            return Dispatcher::simpleMapping($command, 'Hifone', 'Hifone\Handlers');
        });

        $dispatcher->pipeThrough([UseDatabaseTransactions::class]);

        Str::macro('canonicalize', function ($url) {
            return preg_replace('/([^\/])$/', '$1/', $url);
        });

        Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            if(preg_match('/^1[34578]\d{9}$/', $value)){
                return true;
            }
            return false;
        });

        Carbon::setLocale('zh');

        \DB::listen(function($event) {
            if (env('APP_ENV', 'production') == 'local') {
                foreach ($event->bindings as $key => $binding) {
                    if ($binding instanceof \DateTime) {
                        $event->bindings[$key] = $binding->format('Y/m/d H:i:s');
                    }
                }
                $sql = str_replace(['?'], ["'%s'"], $event->sql);
                $log = vsprintf($sql, $event->bindings);
                \Log::info($log);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerDateFactory();
    }

    /**
     * Register the date factory.
     *
     * @return void
     */
    protected function registerDateFactory()
    {
        $this->app->singleton(DateFactory::class, function ($app) {
            $appTimezone = $app->config->get('app.timezone');
            $hifoneTimezone = $app->config->get('hifone.timezone');

            return new DateFactory($appTimezone, $hifoneTimezone);
        });
    }
}
