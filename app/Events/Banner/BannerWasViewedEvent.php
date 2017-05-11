<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Banner;

use Hifone\Events\EventInterface;
use Hifone\Models\Carousel;

final class BannerWasViewedEvent implements EventInterface
{
    /**
     * The thread that has been reported.
     *
     * @var \Hifone\Models\Carousel
     */
    public $carousel;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(Carousel $carousel)
    {
        $this->carousel = $carousel;
    }
}
