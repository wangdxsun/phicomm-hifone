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

use Hifone\Models\User;

final class FavoritedWasRemovedEvent implements FavoriteEventInterface
{
    /**
     * The favorite that has been reported.
     *
     * @var \Hifone\Models\Favorite
     */
    public $user;

    /**
     * Create a new favorite has reported event instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
