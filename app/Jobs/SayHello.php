<?php

namespace Hifone\Jobs;

use Hifone\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SayHello extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::debug('Hello '.$this->name);
    }
}
