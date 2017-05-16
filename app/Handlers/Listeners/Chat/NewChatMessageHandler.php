<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Listeners\Chat;

use Hifone\Events\Chat\NewChatMessageEvent;
use Hifone\Models\Chat;

class NewChatMessageHandler
{
    public function handle(NewChatMessageEvent $event)
    {
        Chat::create([
            'from' => $event->from->id,
            'to' => $event->to->id,
            'from_to' => $event->from->id * $event->to->id,
            'message' => $event->message,
        ]);
    }
}
