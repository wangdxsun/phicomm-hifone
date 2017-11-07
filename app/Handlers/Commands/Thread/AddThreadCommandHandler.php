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
use Hifone\Commands\Thread\AddThreadCommand;
use Hifone\Models\Thread;
use Hifone\Services\Tag\AddTag;
use Illuminate\Support\Str;

class AddThreadCommandHandler
{
    /**
     * Handle the report thread command.
     *
     * @param \Hifone\Commands\Thread\AddThreadCommand $command
     *
     * @return \Hifone\Models\Thread
     */
    public function handle(AddThreadCommand $command)
    {
        $thumbnails = $this->getFirstImageUrl($command->body.$command->images);
        $body = app('parser.markdown')->convertMarkdownToHtml($command->body);
        $body .= $command->images;
        $data = [
            'user_id'       => $command->user_id,
            'title'         => $command->title,
            'excerpt'       => Thread::makeExcerpt($command->body),
            'node_id'       => $command->node_id,
            'sub_node_id'   => $command->sub_node_id,
            'body'          => $body,
            'body_original' => $command->body,
            'created_at'    => Carbon::now()->toDateTimeString(),
            'updated_at'    => Carbon::now()->toDateTimeString(),
            'thumbnails'    => $thumbnails,
            'ip'            => getClientIp().':'.$_SERVER['REMOTE_PORT'],
            'channel'       => $command->channel,
            'dev_info'      => $command->dev_info,
            'contact'       => $command->contact
        ];
        // Create the thread
        $thread = Thread::create($data);

        // The thread was added successfully, so now let's deal with the tags.
        app(AddTag::class)->attach($thread, $command->tags);

        return $thread;
    }

    public function getFirstImageUrl($body) {
        preg_match_all('/src=["\']{1}([^"^\']*)["\']/i', $body, $images);
        $imgUrls = [];
        if (count($images) > 0) {
            foreach ($images[1] as $k => $v) {
                $imgUrls[] = $v;
            }
        }
        $imgUrl = array_first($imgUrls,function($key,$value) {
            if (!(Str::contains($value, 'icon_apk')) && !(Str::contains($value, 'icon_bin')) && !(Str::contains($value, 'icon_word'))) {
                return $value;
            }
            return null;
        });
        return $imgUrl;
    }
}
