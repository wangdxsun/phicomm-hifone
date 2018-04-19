<?php
namespace Hifone\Events\Credit;

use Hifone\Events\EventInterface;

final class LevelUpEvent implements EventInterface
{
    /**
     * The credit that has been reported.
     *
     * @var \Hifone\Models\Credit
     */
    public $user;
    public $credit;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct($user,$credit)
    {
        $this->user = $user;
        $this->credit = $credit;
    }
}