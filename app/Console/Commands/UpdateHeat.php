<?php

namespace Hifone\Console\Commands;

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Thread::visible()->heat()->chunck(200, function ($threads) {
            foreach ($threads as $thread) {
                $thread->heat = $thread->heat_compute;
                $thread->save();
            }
        });
    }
}
