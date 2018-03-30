<?php

namespace Hifone\Events\Thread;

use Hifone\Models\Thread;

final class ThreadWasNodePinnedEvent implements ThreadEventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Thread
     */
    public $user;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(Thread $thread)
    {
        $this->target = $thread;
    }
}