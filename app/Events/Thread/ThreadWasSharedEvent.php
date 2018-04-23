<?php
namespace Hifone\Events\Thread;

use Hifone\Models\Thread;

final class ThreadWasSharedEvent implements ThreadEventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Thread
     */
    public $thread;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }
}
