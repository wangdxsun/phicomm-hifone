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

use Hifone\Events\Event;
use Hifone\Events\EventInterface;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct($message)
    {
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
