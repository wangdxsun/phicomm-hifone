<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Pin;

use Hifone\Models\User;

final class SinkWasAddedEvent implements PinEventInterface
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
