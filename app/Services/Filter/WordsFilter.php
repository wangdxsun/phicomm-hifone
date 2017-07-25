<?php

namespace Hifone\Services\Filter;

use DB;
use Cache;

use Hifone\Models\Word;

class WordsFilter
{
    private $wordInit;

    public function __construct(WordInit $wordInit) {
        $this->wordInit = $wordInit;
    }

    public function wordsFilter($post) {
        $cacheTime = 30 * 24 * 60;
        $tree = Cache::remember('wordsBanned', $cacheTime, function () {
            $words = Word::where('status', '禁止关键词')->pluck('word');
            return $this->wordInit->initKeyWord($words);
        });
        $res = $this->wordInit->isContainBadWords($post, $tree);
        if ($res) {
            return ['type' => '禁止关键词', 'word' => $res];
        }
        $tree = Cache::remember('wordsCheck', $cacheTime, function () {
            $words = Word::where('status', '审核关键词')->pluck('word');
            return $this->wordInit->initKeyWord($words);
        });
        $res = $this->wordInit->isContainBadWords($post, $tree);
        if ($res) {
            return ['type' => '审核关键词', 'word' => $res];
        }
        $tree = Cache::remember('wordsReplace', $cacheTime, function () {
            $words = Word::where('status', '替换关键词')->pluck('word');
            return $this->wordInit->initKeyWord($words);
        });
        $res = $this->wordInit->isContainBadWords($post, $tree);
        if ($res) {
            $replace_post = $this->wordInit->replaceBadWords($post);
            return ['type' => '替换关键词', 'word' => $replace_post];
        }
        return ['type' => '没有敏感词', 'word' => ''];
    }
}
