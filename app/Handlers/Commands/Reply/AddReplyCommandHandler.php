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
use Hifone\Models\User;

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
     * @param \Hifone\Services\Commands\Reply\AddReplyCommand $command
     *
     * @return \Hifone\Models\Reply
     */
    public function handle(AddReplyCommand $command)
    {
        $data = [
            'user_id'       => $command->user_id,
            'thread_id'     => $command->thread_id,
            'body'          => app('parser.markdown')->convertMarkdownToHtml(app('parser.at')->parse($command->body)),
            'body_original' => $command->body,
            'created_at'    => Carbon::now()->toDateTimeString(),
            'updated_at'    => Carbon::now()->toDateTimeString(),
        ];
        // Create the reply
        $reply = Reply::create($data);

         // Add the reply user
        if ($reply->thread) {
            $reply->thread->last_reply_user_id = $reply->user_id;
            $reply->thread->reply_count++;
            $reply->thread->updated_at = Carbon::now()->toDateTimeString();
            $reply->thread->save();
        }

        $reply->user->increment('reply_count', 1);

        event(new ReplyWasAddedEvent($reply));

        $thread = Thread::find($command->thread_id);
        $user = User::find($thread->user_id);

        event(new RepliedWasAddedEvent($user));

        return $reply;
    }
}
