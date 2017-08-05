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

use Auth;
use Carbon\Carbon;
use Hifone\Commands\Thread\AddThreadCommand;
use Hifone\Events\Thread\ThreadWasAddedEvent;
use Hifone\Models\Thread;
use Hifone\Services\Dates\DateFactory;
use Hifone\Services\Tag\AddTag;
use Illuminate\Support\Str;

class AddThreadCommandHandler
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
     * @param \Hifone\Commands\Thread\AddThreadCommand $command
     *
     * @return \Hifone\Models\Thread
     */
    public function handle(AddThreadCommand $command)
    {

        $body = app('parser.markdown')->convertMarkdownToHtml(app('parser.at')->parse($command->body));
        $command->thumbnails = $this->getFirstImageUrl($body);
        $body = app('parser.emotion')->parse($body);
        $body = "$body".$command->images;
        $data = [
            'user_id'       => $command->user_id,
            'title'         => $command->title,
            'excerpt'       => Thread::makeExcerpt($command->body),
            'node_id'       => $command->node_id,
            'body'          => $body,
            'body_original' => $command->body,
            'created_at'    => Carbon::now()->toDateTimeString(),
            'updated_at'    => Carbon::now()->toDateTimeString(),
            'thumbnails'    => $command->thumbnails,
        ];
        // Create the thread
        $thread = Thread::create($data);

        // The thread was added successfully, so now let's deal with the tags.
        app(AddTag::class)->attach($thread, $command->tags);

        return $thread;
    }

    public function getFirstImageUrl($body) {
        preg_match_all('/src="([^"]*)\"/i', $body, $images);
        $imgUrls = [];
        if (count($images) > 0) {
            foreach ($images[1] as $k => $v) {
                $imgUrls[] = $v;
            }
        }
        return array_first($imgUrls);
    }
}
