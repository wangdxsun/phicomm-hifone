<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/4
 * Time: 9:43
 */

namespace Hifone\Commands\Question;

final class AddQuestionCommand
{
    public $title;
    public $body;
    public $userId;
    public $tagIds;
    public $score;
    public $device;
    public $ip;

    public function __construct($title, $body, array $tagIds, $score, $userId, $device = '', $ip = '')
    {
        $this->title =$title;
        $this->body = $body;
        $this->userId = $userId;
        $this->tagIds = $tagIds;
        $this->score = $score;
        $this->device = $device;
        $this->ip = $ip;
    }
}