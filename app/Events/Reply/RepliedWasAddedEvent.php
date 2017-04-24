<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Reply;

final class RepliedWasAddedEvent implements ReplyEventInterface
{

    public $threadUser;

    public $replyUser;

    public function __construct($replyUser, $threadUser)
    {
        $this->replyUser = $replyUser;
        $this->threadUser = $threadUser;
    }
}
