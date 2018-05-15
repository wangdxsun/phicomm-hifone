<?php
namespace Hifone\Events\Question;

use Hifone\Models\Question;
use Hifone\Models\User;

final class QuestionWasAuditedEvent implements QuestionEventInterface
{
    public $question;
    public $user;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(User $user, $question)
    {
        $this->question = $question;
        $this->user = $user;
    }
}