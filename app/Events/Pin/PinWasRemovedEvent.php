<?php
namespace Hifone\Events\Pin;

use Hifone\Models\User;

//取消置顶
final class PinWasRemovedEvent implements PinEventInterface
{
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