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
use Hifone\Models\Reply;

class AddReplyCommandHandler
{

    /**
     * Handle the report thread command.
     *
     * @param \Hifone\Commands\Reply\AddReplyCommand $command
     *
     * @return \Hifone\Models\Reply
     */
    public function handle(AddReplyCommand $command)
    {
        //如果有单独上传图片，将图片拼接到正文后面
        $command->body .= $command->images;

        $data = [
            'user_id'       => $command->user_id,
            'thread_id'     => $command->thread_id,
            'reply_id'      => $command->reply_id,
            'body'          => $command->body,
            'body_original' => $command->body,
            'created_at'    => Carbon::now()->toDateTimeString(),
            'updated_at'    => Carbon::now()->toDateTimeString(),
            'ip'            => getClientIp().':'.$_SERVER['REMOTE_PORT'],
            'channel'       => $command->channel,
            'dev_info'      => $command->dev_info,
            'contact'       => $command->contact,
        ];
        // Create the reply
        $reply = Reply::create($data);
        $reply = Reply::find($reply->id);

        return $reply;
    }
}
