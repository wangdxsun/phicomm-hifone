<?php
namespace Hifone\Events\User;

use Hifone\Models\User;

final class AppUserWasActiveEvent implements UserEventInterface
{
    /**
     * The user that has been logged in.
     *
     * @var \Hifone\Models\User
     */
    public $user;

    /**
     * Create a new user has reported event instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}