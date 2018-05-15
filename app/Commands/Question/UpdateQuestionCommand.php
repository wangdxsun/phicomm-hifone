<?php
namespace Hifone\Commands\Question;

final class UpdateQuestionCommand
{
    public $question;
    public $data;

    public function __construct($question, $data)
    {
        $this->question = $question;
        $this->data = $data;
    }
}