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

    private $queue;

    public function __construct(User $from, User $to, $message, $queue = 'default')
    {
        $this->from = $from;
        $this->to = $to;
        $this->message = $message;
        $this->queue = $queue;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * ShouldBroadcast将事件标示为广播 broadcastOn方法返回一个必须被广播的频道的名称数组
*/
    public function broadcastOn()
    {
        return ['messages'];
    }

    public function broadcastAs()
    {
        return 'newMessage';
    }

    public function onQueue()
    {
        return $this->queue;
    }
}
