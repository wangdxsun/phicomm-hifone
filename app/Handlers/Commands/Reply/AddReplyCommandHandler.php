<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Handlers\Commands\Reply;

use Carbon\Carbon;
use Hifone\Commands\Reply\AddReplyCommand;
use Hifone\Events\Reply\ReplyWasAddedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Models\Reply;
use Hifone\Services\Dates\DateFactory;
use Hifone\Models\Thread;

class AddReplyCommandHandler
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

    /**
     * Handle the report thread command.
     *
     * @param \Hifone\Commands\Reply\AddReplyCommand $command
     *
     * @return \Hifone\Models\Reply
     */
    public function handle(AddReplyCommand $command)
    {
        $body = app('parser.markdown')->convertMarkdownToHtml(app('parser.at')->parse($command->body));
        $body = app('parser.emotion')->parse($body);
        $data = [
            'user_id'       => $command->user_id,
            'thread_id'     => $command->thread_id,
            'body'          => $body,
            'body_original' => $command->body,
            'created_at'    => Carbon::now()->toDateTimeString(),
            'updated_at'    => Carbon::now()->toDateTimeString(),
        ];
        // Create the reply
        $reply = Reply::create($data);
        return $reply;
    }
}
