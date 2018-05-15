<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Listeners\Stats;

use Cache;
use Hifone\Events\EventInterface;
use Hifone\Events\Image\ImageWasUploadedEvent;
use Hifone\Events\Reply\ReplyWasAuditedEvent;
use Hifone\Events\Thread\ThreadWasAuditedEvent;
use Hifone\Events\User\UserWasAddedEvent;
use Hifone\Models\Stats;

class UpdateStatsHandler
{
    public function handle(EventInterface $event)
    {
        $key = 'stats';
        if ($event instanceof ReplyWasAuditedEvent) {
            Stats::newReply();
        } elseif ($event instanceof ThreadWasAuditedEvent) {
            Stats::newThread();
        } elseif ($event instanceof UserWasAddedEvent) {
            Stats::newUser();
        } elseif ($event instanceof ImageWasUploadedEvent) {
            Stats::newImage();
        }

        Cache::forget($key);
    }
}
