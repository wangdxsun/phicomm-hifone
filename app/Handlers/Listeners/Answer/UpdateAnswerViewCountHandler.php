<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/18
 * Time: 15:39
 */
namespace Hifone\Handlers\Listeners\Answer;

use Hifone\Events\Answer\AnswerWasViewedEvent;
use Hifone\Models\Answer;

class UpdateAnswerViewCountHandler
{
    protected $cache_key = 'answers_viewed';

    public function handle(AnswerWasViewedEvent $event)
    {
        $answer = $event->answer;

        if ($answer->status == Answer::VISIBLE && !$this->hasViewedAnswer($answer)) {
            $answer->increment('view_count', 1);
            $this->storeViewedAnswer($answer);
        }
    }

    protected function hasViewedAnswer($answer)
    {
        return array_key_exists($answer->id, $this->getViewedAnswers());
    }

    protected function getViewedAnswers()
    {
        return app('session')->get($this->cache_key, []);
    }

    protected function storeViewedAnswer($answer)
    {
        $key = $this->cache_key.'.'.$answer->id;

        app('session')->put($key, time());
    }

}