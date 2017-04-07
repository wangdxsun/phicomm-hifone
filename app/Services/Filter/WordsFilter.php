<?php

namespace Hifone\Services\Filter;

use DB;

class WordsFilter
{
    private $words_init;
    public function __construct() {
        $this->words_init = new WordInit();
    }
    public function wordsFilter($post){
        $words_banned=DB::table('words')->where('replacement','=','{BANNED}')->pluck('find');
        //$words_banned=DB::table('words')->where('replacement','=','禁止关键词')->pluck('find');
        $this->words_init->initKeyWord($words_banned);
        $res1 = $this->words_init->isContainBadWords($post);
        if($res1){
            return 1;
        }else{
            $words_check=DB::table('words')->where('replacement','=','{MOD}')->pluck('find');
            //$words_check=DB::table('words')->where('replacement','=','审核关键词')->pluck('find');
            $this->words_init->initKeyWord($words_check);
            $res2 = $this->words_init->isContainBadWords($post);
            if($res2){
                return 2;
            }else{
                $words_replace=DB::table('words')->where('replacement','=','{REPLACE}')->pluck('find');
                //$words_replace = DB::table('words')->where('replacement','=','替换关键词')->pluck('find');
                $this->words_init->initKeyWord($words_replace);
                $res3 = $this->words_init->isContainBadWords($post);

                if($res3){
                    $replace_post = $this->words_init->replaceBadWords($post);
                    return $replace_post;
                }else{
                    return 0;
                }
            }
        }
    }
}
