<?php
namespace Hifone\Services\Parsers;

use Hifone\Models\Emotion;


class ParseEmotion
{
    public $url_parsed;
    public $emotions = [];
    public $userEmotions;
    public $url;

    public function parse($emotion)
    {
        $this->url = $emotion;
        $this->userEmotions = $this->getEmotions();

        count($this->userEmotions) > 0 && $this->emotions = Emotion::whereIn('emotion', $this->userEmotions)->get();

        $this->replace();

        return $this->url_parsed;
    }

    protected function replace()
    {
        $this->url_parsed = $this->url;

        foreach ($this->emotions as $emotion) {
            $search = $emotion->emotion;
            $replace = '<img class="face" src ='.$emotion->url.'>';

            $this->url_parsed = str_replace($search, $replace, $this->url_parsed);
        }
    }

    protected function getEmotions()
    {
        preg_match_all("/\[([^]@<\r\n\s]*)\]/i", $this->url, $atlist_tmp);
        $userEmotions = [];

        foreach ($atlist_tmp[1] as $k => $v) {
            $userEmotions[] = '['.$v.']';
        }

        return array_unique($userEmotions);
    }
}