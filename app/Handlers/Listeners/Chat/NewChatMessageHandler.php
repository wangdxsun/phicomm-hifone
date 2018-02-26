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
use Input;

class NewChatMessageHandler
{
    public function handle(NewChatMessageEvent $event)
    {
        Chat::create([
            'from_user_id' => $event->from->id,
            'to_user_id' => $event->to->id,
            'from_to' => $event->from->id * $event->to->id + $event->from->id + $event->to->id,
            'message' => $event->message,
        ]);

        $event->to->increment('notification_chat_count', 1);
        //友盟消息推送

        $message = mb_substr(app('parser.emotion')->reverseParseEmotionAndImage($event->message), 0, 100);
        $data = [
            "content" => $message,
            "type" => '1004',
            "source" => '1',
            "producer" => '2',
            "isBroadcast" => '0',
            "isMulticast" => '0',
            "avatar" => $event->from->avatar_url,
            "title" => $event->from->username,
            "time" => date('Y-m-d H:i', strtotime('now')),
            "userId" => $event->from->id,
        ];

        app('push')->push($event->to->phicomm_id, $data, $message);
    }
}
