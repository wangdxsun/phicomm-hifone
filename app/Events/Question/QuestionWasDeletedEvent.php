<?php
namespace Hifone\Events\Question;

use Hifone\Models\Question;
use Hifone\Models\User;

final class QuestionWasDeletedEvent implements QuestionEventInterface
{
    public $question;
    public $user;

    public function __construct(User $user, Question $question)
    {
        $this->question = $question;
        $this->user = $user;
    }
}