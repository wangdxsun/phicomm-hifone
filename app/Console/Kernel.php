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

use Hifone\Console\Commands\AddAutoTag;
use Hifone\Console\Commands\AutoAdopt;
use Hifone\Console\Commands\GetRank;
use Hifone\Console\Commands\InitNodesThreadAndReplyCount;
use Hifone\Console\Commands\GetThumbnails;
use Hifone\Console\Commands\InitSubNode;
use Hifone\Console\Commands\RemindAdopt;
use Hifone\Console\Commands\SearchImport;
use Hifone\Console\Commands\SearchMapping;
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
        AddAutoTag::class,
        GetRank::class,
        SendMessage::class,
        UpdateHeat::class,
        SearchImport::class,
        InitSubNode::class,
        GetThumbnails::class,
        InitNodesThreadAndReplyCount::class,
        RemindAdopt::class,
        AutoAdopt::class,
        SearchMapping::class
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
        $schedule->command('heat:update')->everyFiveMinutes();
        $schedule->command('get:rank')->weekly()->mondays()->at('0:0');
        $schedule->command('add:autoTag')->daily()->at('1:0');
        //        $schedule->command('remind:adopt')->everyThirtyMinutes();
        $schedule->command('remind:adopt')->everyFiveMinutes();
        //        $schedule->command('auto:adopt')->hourly();
        $schedule->command('auto:adopt')->everyFiveMinutes();
    }
}
