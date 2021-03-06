<?php

namespace Hifone\Events\Answer;

use Hifone\Models\User;
//回答审核通过
final class AnswerWasAuditedEvent implements AnswerEventInterface
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