<?php
namespace Hifone\Services\Parsers;

use Hifone\Models\Emotion;


class ParseEmotion
{
    public $emotions = [];
    public $userEmotions;
    public $post;

    public function parse($post)
    {
        $this->post= $post;

        $this->userEmotions = $this->getEmotions();

        count($this->userEmotions) > 0 && $this->emotions = Emotion::whereIn('emotion', $this->userEmotions)->get();

        $this->replace();

        return $this->post;
    }

    protected function replace()
    {
        foreach ($this->emotions as $emotion) {
            $search = $emotion->emotion;
            $replace = '<img class="face" src="'.request()->getSchemeAndHttpHost().$emotion->url.'">';

            $this->post = str_replace($search, $replace, $this->post);
        }
    }

    protected function getEmotions()
    {
        preg_match_all("/\[([^]@<\r\n\s]*)\]/i", $this->post, $atlist_tmp);
        $userEmotions = [];

        foreach ($atlist_tmp[1] as $k => $v) {
            $userEmotions[] = '['.$v.']';
        }

        return array_unique($userEmotions);
    }
}