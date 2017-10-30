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
use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Commands\Reply\AddReplyCommand;
use Hifone\Models\Reply;
use Hifone\Services\Dates\DateFactory;
use Input;

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
        $body = app('parser.markdown')->convertMarkdownToHtml($command->body);
        //如果有单独上传图片，将图片拼接到正文后面
        $body .= $command->images;
        $command->body .= $command->images;

        $data = [
            'user_id'       => $command->user_id,
            'thread_id'     => $command->thread_id,
            'reply_id'      => $command->reply_id,
            'body'          => $body,
            'body_original' => $command->body,
            'created_at'    => Carbon::now()->toDateTimeString(),
            'updated_at'    => Carbon::now()->toDateTimeString(),
            'ip'            => getClientIp().':'.$_SERVER['REMOTE_PORT'],
        ];
        // Create the reply
        $reply = Reply::create($data);
        return $reply;
    }
}
