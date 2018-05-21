<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/18
 * Time: 15:50
 */

namespace Hifone\Events\Answer;

use Hifone\Models\Answer;

final class AnswerWasViewedEvent implements AnswerEventInterface
{
    /**
     * The question that has been viewed.
     *
     * @var \Hifone\Models\Question
     */
    public $answer;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(Answer $answer)
    {
        $this->answer = $answer;
    }

}