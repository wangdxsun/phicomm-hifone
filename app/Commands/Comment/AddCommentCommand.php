<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/16
 * Time: 16:03
 */

namespace Hifone\Commands\Comment;

use Hifone\Models\Answer;

final class AddCommentCommand
{
    public $body;

    public $userId;

    public $answerId;

    public $commentId;

    public $device;

    public $ip;

    public $status;

    public function __construct($body, $userId, $answerId, $commentId, $device = '', $ip = '', $status = Answer::AUDIT)
    {
        $this->body = $body;
        $this->userId = $userId;
        $this->answerId = $answerId;
        $this->commentId = $commentId;
        $this->device = $device;
        $this->ip = $ip;
        $this->status = $status;
    }
}