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


//被收藏，被动操作
final class FavoritedWasAddedEvent implements FavoriteEventInterface
{
    public $object;

    public function __construct($object)
    {
        $this->object = $object;
    }
}
