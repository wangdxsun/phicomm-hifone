<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Listeners\Thread;

use Hifone\Events\Thread\ThreadWasMovedEvent;
use Hifone\Models\Thread;

class UpdateThreadNodesHandler
{
    public function handle(ThreadWasMovedEvent $event)
    {
        $thread = $event->target;
        //此时帖子的信息已经被修改，thread中保存的是最新的子版块信息
        $targetSubNode = $thread->subNode;
        $originalSubNode = $event->originalSubNode;

        $targetSubNode->update(['thread_count' => $targetSubNode->threads()->visible()->count()]);
        $originalSubNode->update(['thread_count' => $originalSubNode->threads()->visible()->count()]);

        if ($targetSubNode->node != $originalSubNode->node) {
            $targetSubNode->node->update(['thread_count' => $targetSubNode->node->threads()->visible()->count()]);
            $originalSubNode->node->update(['thread_count' => $originalSubNode->node->threads()->visible()->count()]);
        }

    }
}
