<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Events\Image;

final class AvatarWasUploadedEvent implements ImageEventInterface
{
    public $target;

    public function __construct($target)
    {
        $this->target = $target;
    }
}
