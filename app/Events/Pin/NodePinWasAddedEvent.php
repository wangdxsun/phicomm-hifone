<?php
namespace Hifone\Events\Pin;

use Hifone\Models\User;

final class NodePinWasAddedEvent implements PinEventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Thread
     */
    public $user;
    public $object;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(User $user, $object)
    {
        $this->user = $user;
        $this->object = $object;
    }
}