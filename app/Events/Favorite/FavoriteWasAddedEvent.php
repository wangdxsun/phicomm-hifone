<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Favorite;

use Hifone\Models\Thread;
use Hifone\Models\User;
//帖子被收藏，被动操作
final class FavoriteWasAddedEvent implements FavoriteEventInterface
{
    public $thread;

    public function __construct(Thread $thread)
    {
        $this->thread = $thread;
    }
}
