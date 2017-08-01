<?php

namespace Hifone\Services\Filter;

use Hifone\Models\Word;
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
    private $trieTree;

    public function __construct(TrieTree $trieTree) {
        $this->trieTree = $trieTree;
    }

    //多个敏感词刷新字典树
    public function initKeyWord($words){
        return $this->trieTree->importBadWords($words);
    }

    //插入一个词
    public function insertWord($word) {
        return $this->trieTree->insert($word);
    }

    //替换一个词
    public function replaceWord($beforeWord, $afterWord) {
        $this->trieTree->remove($beforeWord);
        return $this->trieTree->insert($afterWord);
    }

    //移除一个词
    public function removeWord($word) {
        return $this->trieTree->remove($word);
    }

    public function isContainBadWords($post, $tree) {
        return $this->trieTree->contain($post, $tree);
    }

    public function replaceBadWords($post) {
        $replace_array = $this->trieTree->replace($post);
        $replaced_post = $post;
        foreach ($replace_array as $key => $value) {
            $char_fragment = mb_substr($post, $key, $value[$key]-$key+1, 'utf-8');
            $substitute = Word::where('word', $char_fragment)->pluck('replacement');
            $replaced_post = str_replace($char_fragment, $substitute[0], $replaced_post);
        }
        return $replaced_post;
    }

}
