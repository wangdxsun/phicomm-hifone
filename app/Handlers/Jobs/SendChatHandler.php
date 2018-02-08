<?php

namespace Hifone\Handlers\Jobs;

use Hifone\Events\Chat\NewChatMessageEvent;
use Hifone\Jobs\SendChat;

class SendChatHandler
{
    public function handle(SendChat $sendChat)
    {
        event(new NewChatMessageEvent($sendChat->from, $sendChat->to, $sendChat->message));
    }

}