<?php
namespace Hifone\Services\Parsers;

use Hifone\Models\Emotion;

class ParseEmotion
{
    public $body_parsed;
    public $emotions = [];
    public $userEmotions;
    public $body_original;

    public function parse($body)
    {
        $this->body_original = $body;
        $this->userEmotions = $this->getEmotions();

        count($this->userEmotions) > 0 && $this->emotions = Emotion::whereIn('body', $this->userEmotions)->get();

        $this->replace();

        return $this->body_parsed;
    }

    protected function replace()
    {
        $this->body_parsed = $this->body_original;

        foreach ($this->emotions as $emotion) {
            $search = '['.$emotion->body.']';
            $replace = $emotion->body_original;

            $this->body_parsed = str_replace($search, $replace, $this->body_parsed);
        }
    }

    protected function getEmotions()
    {
        preg_match_all("/\[([^]@<\r\n\s]*)\]/i", $this->body_original, $atlist_tmp);
        $userEmotions = [];

        foreach ($atlist_tmp[1] as $k => $v) {
            $userEmotions[] = $v;
        }

        return array_unique($userEmotions);
    }
}