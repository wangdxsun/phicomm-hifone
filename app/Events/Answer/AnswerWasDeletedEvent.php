<?php
namespace Hifone\Events\Answer;

use Hifone\Models\User;

final class AnswerWasDeletedEvent implements AnswerEventInterface
{
    public $answer;
    public $user;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(User $user, $answer)
    {
        $this->answer = $answer;
        $this->user = $user;
    }
}