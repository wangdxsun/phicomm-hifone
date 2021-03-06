<?php

namespace Hifone\Console\Commands;

use Hifone\Exceptions\Handler;
use Hifone\Models\Thread;
use Illuminate\Console\Command;

class UpdateHeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heat:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update heat value of threads timely';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(Handler $handler)
    {
        Thread::visible()->heat()->chunk(200, function ($threads) use ($handler) {
            foreach ($threads as $thread) {
                try {
                    $thread->heat = $thread->heat_compute;
                    $thread->save();
                } catch (\Exception $e) {
                    \Log::info('UpdateHeat:thread', $thread->toArray());
                    $handler->report($e);
                }
            }
        });
    }
}
