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

use Carbon\Carbon;
use Hifone\Commands\Thread\UpdateThreadCommand;
use Hifone\Events\Thread\ThreadWasMarkedExcellentEvent;
use Hifone\Events\Thread\ThreadWasMovedEvent;
use Hifone\Exceptions\HifoneException;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Services\Dates\DateFactory;
use Hifone\Services\Tag\AddTag;
use Auth;

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
        //更新编辑时间 if (created_at != edit_time) 帖子被修改过
        $command->data['edit_time'] = Carbon::now()->toDateTimeString();
        //普通用户编辑状态回退、精华失效
        if (!Auth::user()->hasRole(['Admin', 'Founder'])) {
            $command->data['status'] = Thread::AUDIT;
            $command->data['is_excellent'] = 0;
        }
        //投票贴相关参数（除选项外）
        unset($command->data['options']);
        if ($thread->is_vote == 0) {//非投票贴
            unset($command->data['option_max']);
            unset($command->data['vote_start']);
            unset($command->data['vote_end']);
            unset($command->data['vote_level']);
            unset($command->data['view_voting']);
            unset($command->data['view_vote_finish']);
        }

        $thread->update($this->filter($command->data));
        $tags = isset($command->data['tags']) ? $command->data['tags'] : [];
        app(AddTag::class)->attach($thread, $tags);
        if (isset($command->data['sub_node_id']) && $original_subNode_id != intval($command->data['sub_node_id']) ) {
            $originalSubNode = SubNode::find($original_subNode_id);
            event(new ThreadWasMovedEvent($thread, $originalSubNode));
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
