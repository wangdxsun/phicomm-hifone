<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Like;

final class LikedWasRemovedEvent implements LikeEventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Like
     */
    public $user;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
