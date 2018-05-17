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

//被赞 （帖子，评论，提问，回答）
final class LikedWasAddedEvent implements LikeEventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Like
     */
    public $user;
    public $object;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct($user, $object=null)
    {
        $this->user = $user;
        $this->object = $object;
    }
}
