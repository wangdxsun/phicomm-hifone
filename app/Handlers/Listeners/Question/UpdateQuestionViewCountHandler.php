<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/18
 * Time: 15:39
 */
namespace Hifone\Handlers\Listeners\Question;

use Hifone\Events\Question\QuestionWasViewedEvent;
use Hifone\Models\Question;

class UpdateQuestionViewCountHandler
{
    protected $cache_key = 'questions_viewed';

    public function handle(QuestionWasViewedEvent $event)
    {
        $question = $event->question;

        if ($question->status == Question::VISIBLE && !$this->hasViewedQuestion($question)) {
            $question->increment('view_count', 1);
            $this->storeViewedQuestion($question);
        }
    }

    protected function hasViewedQuestion($question)
    {
        return array_key_exists($question->id, $this->getViewedQuestions());
    }

    protected function getViewedQuestions()
    {
        return app('session')->get($this->cache_key, []);
    }

    protected function storeViewedQuestion($question)
    {
        $key = $this->cache_key.'.'.$question->id;

        app('session')->put($key, time());
    }

}