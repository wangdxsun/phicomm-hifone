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

final class LikeWasAddedEvent implements LikeEventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Like
     */
    public $target;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct($target)
    {
        $this->target = $target;
    }
}
