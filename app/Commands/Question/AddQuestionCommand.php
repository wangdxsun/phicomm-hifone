<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/4
 * Time: 9:43
 */

namespace Hifone\Commands\Question;

use Hifone\Models\Question;

final class AddQuestionCommand
{
    public $title;

    public $body;

    public $userId;

    public $tagIds;

    public $score;

    public $device;

    public $ip;

    public $status;

    public function __construct($title, $body, array $tagIds, $score, $userId, $device = '', $ip = '', $status = Question::AUDIT)
    {
        $this->title =$title;
        $this->body = $body;
        $this->userId = $userId;
        $this->tagIds = $tagIds;
        $this->score = $score;
        $this->device = $device;
        $this->ip = $ip;
        $this->status = $status;
    }
}