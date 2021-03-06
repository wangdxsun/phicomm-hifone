<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Commands\Thread;

use Hifone\Models\Thread;

final class AddThreadCommand
{
    public $title;

    public $body;

    public $user_id;

    public $node_id;

    public $sub_node_id;

    public $tags;

    public $images;

    public $thumbnails;

    public $channel;

    public $dev_info;

    public $contact;

    public $status;

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'title'   => 'required|string',
        'body'    => 'required|string',
        'user_id' => 'int',
        'node_id' => 'int',
        'sub_node_id' => 'int',
    ];

    public function __construct($title, $body, $user_id, $node_id, $sub_node_id, $tags, $images = '', $status = Thread::AUDIT, $channel = Thread::THREAD, $dev_info = NULL, $contact = NULL)
    {
        $this->title = $title;
        $this->body = $body;
        $this->user_id = $user_id;
        $this->node_id = $node_id;
        $this->sub_node_id = $sub_node_id;
        $this->tags = $tags;
        $this->images = $images;
        $this->status = $status;
        $this->channel = $channel;
        $this->dev_info = $dev_info;
        $this->contact = $contact;
    }
}
