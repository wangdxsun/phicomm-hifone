<?php

namespace Hifone\Services\Filter;

use Hifone\Services\Filter\Utils\TrieTree;
use DB;
/**
 * @Description: 初始化敏感词库，将敏感词加入到HashMap中，构建DFA算法模型
 * @Project：hifone
 * @Author : Wells
 * @Date ： 2017年3月29日 下午2:27:06
 * @version 1.0
 */
class WordInit
{
    private $trie_tree;

    public function __construct() {
        $this->trie_tree = new TrieTree();
    }
    public function initKeyWord($words){
        $trie_data = $this->trie_tree->importBadWords($words);

        return $trie_data;
    }
    public function isContainBadWords($post){
        $flag = $this->trie_tree->contain($post, 0);

		return $flag;
    }
    public function replaceBadWords($post){
        $replace_array = $this->trie_tree->replace($post);
        $replaced_post = $post;
        foreach($replace_array as $key => $value)
        {
            $char_fragment = mb_substr($post, $key, $value[$key]-$key+1, 'utf-8');
            $substitute = DB::table('words')->where('find','=',$char_fragment)->pluck('substitute');
            $replaced_post = str_replace($char_fragment, $substitute[0], $replaced_post);
        }
        return $replaced_post;
    }

}
