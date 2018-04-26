<?php

namespace Hifone\Events\Thread;


final class ThreadWasNodePinnedEvent implements ThreadEventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Thread
     */
    public $target;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct($target)
    {
        $this->target = $target;
    }
}