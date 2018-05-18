<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/16
 * Time: 16:03
 */

namespace Hifone\Commands\Answer;

use Hifone\Models\Answer;

final class AddAnswerCommand
{
    public $body;

    public $userId;

    public $questionId;

    public $device;

    public $ip;

    public $status;

    public function __construct($body, $userId, $questionId, $device = '', $ip = '', $status = Answer::AUDIT)
    {
        $this->body = $body;
        $this->userId = $userId;
        $this->questionId = $questionId;
        $this->device = $device;
        $this->ip = $ip;
        $this->status = $status;
    }
}