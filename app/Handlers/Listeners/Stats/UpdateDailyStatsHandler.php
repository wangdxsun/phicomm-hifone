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
use Carbon\Carbon;
use Hifone\Events\EventInterface;
use Hifone\Events\Thread\ThreadWasAuditedEvent;
use Hifone\Events\Thread\ThreadWasTrashedEvent;
use Hifone\Events\Reply\ReplyWasAuditedEvent;
use Hifone\Events\Reply\ReplyWasTrashedEvent;

//主板块每日净新增发帖、新增回帖数目
class UpdateDailyStatsHandler
{
    public function handle(EventInterface $event)
    {
        $today = Carbon::today()->toDateString();
        if ($event instanceof ThreadWasAuditedEvent) {//新增帖子（审核通过时）
            $node = $event->thread->node;
            if (0 == $node->dailyStats()->where('date', $today)->count()) {
                $node->dailyStats()->create(['date' => $today]);
            }
            $node->dailyStats()->where('date', $today)->increment('thread_count', 1);
        } else if ($event instanceof ReplyWasAuditedEvent) {//新增回复（审核通过时）
            $node = $event->reply->thread->node;
            if (0 == $node->dailyStats()->where('date', $today)->count()) {
                $node->dailyStats()->create(['date' => $today]);
            }
            $node->dailyStats()->where('date', $today)->increment('reply_count', 1);

        } else if ($event instanceof ThreadWasTrashedEvent) {//删除帖子（移入垃圾箱）
            $node = $event->thread->node;
            if (0 == $node->dailyStats()->where('date', $today)->count()) {
                $node->dailyStats()->create(['date' => $today]);
            } elseif ($node->dailyStats()->where('date', $today)->first()->thread_count > 0){//新建记录不会执行减操作
                $node->dailyStats()->where('date', $today)->decrement('thread_count', 1);
            }
        } else if ($event instanceof ReplyWasTrashedEvent) {//删除回复（移入垃圾箱）
            $node = $event->reply->thread->node;
            if (0 == $node->dailyStats()->where('date', $today)->count()) {
                $node->dailyStats()->create(['date' => $today]);
            } elseif ($node->dailyStats()->where('date', $today)->first()->reply_count > 0){//新建记录不会执行减操作
                $node->dailyStats()->where('date', $today)->decrement('reply_count', 1);
            }
        }
    }
}
