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
    /**
     * The reply that was removed.
     *
     * @var \Hifone\Models\Reply
     */
    public $user;

    /**
     * Create a new reply was removed event instance.
     *
     * @param \Hifone\Models\Reply $reply
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
