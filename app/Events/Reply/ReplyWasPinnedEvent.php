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

use Hifone\Models\Reply;

final class ReplyWasPinnedEvent implements ReplyEventInterface
{
    /**
     * The reply that has been pinned.
     *
     * @var \Hifone\Models\Reply
     */
    public $user;

    /**
     * Create a new reply has been pinned event instance.
     */
    public function __construct(Reply $reply)
    {
        $this->target = $reply;
    }
}
