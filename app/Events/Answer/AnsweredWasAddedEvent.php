<?php
namespace Hifone\Events\Answer;

use Hifone\Models\User;
//提问被回答
final class AnsweredWasAddedEvent implements AnswerEventInterface
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