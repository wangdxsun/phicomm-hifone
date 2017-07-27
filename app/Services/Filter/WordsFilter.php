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

    public function filter($post) {

        $cacheTime = 30 * 24 * 60; // 单位为分钟

        $tree = Cache::remember('words', $cacheTime, function () {
            $words = Word::pluck('word');
            return $this->wordInit->initKeyWord($words);
        });
        $res = $this->wordInit->isContainBadWords($post, $tree);

        return $res;
    }
}
