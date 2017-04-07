<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Services\Filter\WordsFilter;

class CheckController extends  Controller
{
    public function check(){

        $start = microtime(true)*1000;

        $words_filter = new WordsFilter();
        //$data = WordsFilter::wordReplace();
        $post ="大狼学校";

        $data = $words_filter->wordsFilter($post);

        $end = microtime(true)*1000;

        echo $end - $start;
        dd($data);
    }
}
