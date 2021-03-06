<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Excellent;

final class ExcellentWasAddedEvent implements ExcellentEventInterface
{
    /**
     * The favorite that has been reported.
     *
     * @var \Hifone\Models\Favorite
     */
    public $user;
    public $object;

    /**
     * Create a new favorite has reported event instance.
     */
    public function __construct($user, $object)
    {
        $this->user = $user;
        $this->object = $object;
    }
}
