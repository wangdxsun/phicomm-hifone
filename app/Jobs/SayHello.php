<?php

namespace Hifone\Jobs;

use Hifone\Exceptions\HifoneException;
use Hifone\Models\Thread;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SayHello extends Job implements ShouldQueue, SelfHandling
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

        $data = [
            'author_id'     => 1824,
            'user_id'       => 1755,
            'body'          => 'jql test',
            'type'          => 'followed_user_new_thread',
        ];
        $thread = Thread::find(2145);
        $thread->notifications()->create($data);

//        throw new HifoneException('exception');
    }

    public function failed()
    {
        \Log::debug('failed');
    }
}
