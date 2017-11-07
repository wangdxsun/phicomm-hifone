<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Console;

use Hifone\Console\Commands\GetRank;
use Hifone\Console\Commands\InitNodesThreadAndReplyCount;
use Hifone\Console\Commands\GetThumbnails;
use Hifone\Console\Commands\InitSubNode;
use Hifone\Console\Commands\SearchImport;
use Hifone\Console\Commands\UpdateHeat;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Hifone\Console\Commands\SendMessage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        GetRank::class,
        SendMessage::class,
        UpdateHeat::class,
        SearchImport::class,
        InitSubNode::class,
        GetThumbnails::class,
        InitNodesThreadAndReplyCount::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --sleep=3 --tries=3')->everyMinute();
        $schedule->command('heat:update')->everyFiveMinutes();
        $schedule->command('get:rank')->weekly()->mondays()->at('8:59');
    }
}
