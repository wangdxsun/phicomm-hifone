<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Chat;

use Hifone\Events\EventInterface;
use Hifone\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

final class NewChatMessageEvent implements EventInterface, ShouldBroadcast
{
    public $from;

    public $to;

    public $message;

    public function __construct(User $from, User $to, $message)
    {
        $this->from = $from;
        $this->to = $to;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['messages'];
    }

    public function broadcastAs()
    {
        return 'newMessage';
    }
}
