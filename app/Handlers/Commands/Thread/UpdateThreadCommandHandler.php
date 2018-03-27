<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Commands\Thread;

use Hifone\Commands\Thread\UpdateThreadCommand;
use Hifone\Events\Thread\ThreadWasMarkedExcellentEvent;
use Hifone\Events\Thread\ThreadWasMovedEvent;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Services\Dates\DateFactory;
use Hifone\Services\Tag\AddTag;
use Agent;

class UpdateThreadCommandHandler
{
    /**
     * The date factory instance.
     *
     * @var \Hifone\Services\Dates\DateFactory
     */
    protected $dates;

    /**
     * Create a new report issue command handler instance.
     *
     * @param \Hifone\Services\Dates\DateFactory $dates
     */
    public function __construct(DateFactory $dates)
    {
        $this->dates = $dates;
    }

    public function handle(UpdateThreadCommand $command)
    {
        $thread = $command->thread;
        $original_subNode_id = $thread->sub_node_id;//帖子更新前的子版块id

        if (isset($command->data['body']) && $command->data['body']) {
            $command->data['body_original'] = $command->data['body'];
            $command->data['excerpt'] = Thread::makeExcerpt($command->data['body']);
            $command->data['body'] = app('parser.at')->parse($command->data['body']);
            $command->data['body'] = app('parser.emotion')->parse($command->data['body']);
            //只有H5和app发帖需要自动转义链接，web端不需要
            if (Agent::match('iPhone') || Agent::match('Android')) {
                $command->data['body'] = app('parser.link')->parse($command->data['body']);
            }

            //过滤数据中的空字段，并且更新帖子
            $command->data['thumbnails'] = getFirstImageUrl($command->data['body_original']);
        }
        $thread->update($this->filter($command->data));

        // The thread was added successfully, so now let's deal with the tags.
        $tags = isset($command->data['tags']) ? $command->data['tags'] : [];
        app(AddTag::class)->attach($thread, $tags);

        if (isset($command->data['is_excellent'])) {
            event(new ThreadWasMarkedExcellentEvent($thread));
        }

        if (isset($command->data['sub_node_id']) && $original_subNode_id != intval($command->data['sub_node_id']) ) {
            $originalSubNode = SubNode::find($original_subNode_id);
            event(new ThreadWasMovedEvent($command->thread, $originalSubNode));
        }

        $thread->updateIndex();

        return $thread;
    }

    /**
     * Filter the data.
     *
     * @param array $data
     *
     * @return array
     */
    protected function filter($data)
    {
        return array_filter($data, function ($val) {
            return $val !== null;
        });
    }
}
