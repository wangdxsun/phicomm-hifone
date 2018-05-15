<?php
namespace Hifone\Commands\Answer;

final class UpdateAnswerCommand
{
    public $answer;
    public $data;

    public function __construct($answer, $data)
    {
        $this->answer = $answer;
        $this->data = $data;
    }
}