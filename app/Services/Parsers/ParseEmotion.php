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
            $replace = '<img class="face" src="'.request()->getSchemeAndHttpHost().$emotion->url.'" />';
            $this->post = str_replace($search, $replace, $this->post);
        }
    }

    protected function getEmotions()
    {
        preg_match_all("/\[([^]@<\r\n\s]*)\]/i", $this->post, $emotions_temp);
        $userEmotions = [];
        foreach ($emotions_temp[1] as $k => $v) {
            $userEmotions[] = '['.$v.']';
        }
        return array_unique($userEmotions);
    }

    public function reverseParseEmotionAndImage($post)
    {
        $this->post= $post;
        preg_match_all("/<img\s+class=\"face\"\s+src=\"[^>]*(\/images\/emotion\/face-\w+\.png)\"[^>]*>/i", $post, $emotions_temp);
        $this->userEmotions[0] = array_unique($emotions_temp[0]);
        $this->userEmotions[1] = array_unique($emotions_temp[1]);

        //替换表情
        foreach ($this->userEmotions[1] as $key => $value) {
            $search = $this->userEmotions[0][$key];
            $wordOfEmotion = Emotion::where('url', $value)->first();
            $replace = $wordOfEmotion->emotion;
            $post = str_replace($search, $replace, $post);
        }

        //替换图片
        preg_match_all("/<img[^>]*src=[^>]*>/i", $post, $images_temp);
        if (count($images_temp[0]) > 0) {
            foreach ($images_temp[0] as $image) {
                $search = $image;
                $replace = '[图片]';
                $post = str_replace($search, $replace, $post);
            }
        }

        //替换链接
        preg_match_all("/<a\s+href=[\"|\'][^>]*>([^<]*)<\/a>/i", $post, $links_temp);
        if (count($links_temp[0]) > 0) {
            foreach ($links_temp[0] as $key => $value) {
                $search = $links_temp[0][$key];
                $replace = $links_temp[1][$key];
                $post = str_replace($search, $replace, $post);
            }
        }

        return $post;
    }
}