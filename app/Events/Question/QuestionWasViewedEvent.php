<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/18
 * Time: 15:50
 */

namespace Hifone\Events\Question;

use Hifone\Models\Question;

final class QuestionWasViewedEvent implements QuestionEventInterface
{
    /**
     * The question that has been viewed.
     *
     * @var \Hifone\Models\Question
     */
    public $question;

    /**
     * Create a new thread has reported event instance.
     */
    public function __construct(Question $question)
    {
        $this->question = $question;
    }

}