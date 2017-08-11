<?php
namespace Hifone\Services\Filter\Utils;
/**
 * Created by PhpStorm.
 * User: wei07.wang
 * Date: 2017/3/30
 * Time: 9:53
 */
class TrieTree
{
    public $tree = [];

    public function insert($word)
    {
        $lower = strtolower($word);
        $chars = getChars($lower);
        $count = count($chars);
        $T = &$this->tree;
        for ($i = 0; $i < $count; $i++) {
            $char = $chars[$i];
            if (!array_key_exists($char, $T)) {
                $T[$char] = ['isEnd' =>0];   //插入新字符，关联数组
            }
            if($i == $count - 1) {
                $T[$char]['isEnd'] = 1;
            }
            $T = &$T[$char];
        }
        return $this->tree;
    }

    public function remove($word)
    {
        $lower = strtolower($word);
        $chars = getChars($lower);
        if ($this->_find($chars)) {    //先保证此串在树中
            $count = count($chars);
            $T = &$this->tree;
            for ($i = 0; $i < $count; $i++) {
                $char = $chars[$i];
                if($i == $count - 1) {
                    if (count($T[$char]) == 1) {     //表明仅有此串
                        unset($T[$char]);
                        break;
                    } else {   //存在其他节点
                        $T[$char]['isEnd'] = 0;
                    }
                }
                $T = &$T[$char];
            }
        }
        return $this->tree;
    }

    private function _find(&$chars)
    {
        $count = count($chars);
        $T = &$this->tree;
        for ($i = 0; $i < $count; $i++) {
            $c = $chars[$i];
            if (!array_key_exists($c, $T)) {
                return false;
            }
            $T = &$T[$c];
        }
        return true;
    }

    public function contain($txt, $tree)
    {
        $this->tree = $tree;
        $badWords = '';
        $isEnd = false;
        $lower = strtolower($txt);
        $chars = getChars($lower);
        for($offset = 0; $offset < count($chars); $offset++){
            $this->findChar(array_slice($chars, $offset), $tree, $badWords, $isEnd);
            if ($isEnd) break;
        }
        return $badWords;
    }

    public function findChar($chars, $tree, &$badWords,&$isEnd)
    {
        if ((count($chars) == 0 && array_get($tree, 'isEnd') == 0) || count($tree) == 0) {
            $badWords = '';
            return;
        }
        if (array_key_exists($chars[0], $tree)) {
            $badWords .= $chars[0];
            if ($tree[$chars[0]]['isEnd'] == 0) {
                $this->findChar(array_slice($chars, 1), $tree[$chars[0]], $badWords, $isEnd);
            } else {
                $isEnd = true;
            }
        } else {
            $badWords = '';
        }
    }

    public function replace($txt)
    {
        $replace_array = [];
        $R = &$replace_array;
        //TODO 转小写处理
        $chars = getChars($txt);
        $len = count($chars);
        $T = &$this->tree;
        for ($i = 0; $i < $len; $i++) {
            $c = $chars[$i];
            if (array_key_exists($c, $T)) {     //存在，则判断是否为最后一个
                if($T[$c]['isEnd'] == 1) {       //如果为最后一个匹配规则,结束循环，返回匹配标识数
                    $R[$i] = [
                        $i => $i
                    ];
                }else{
                    for ($k = $i+1; $k < $len; $k++) {
                        $c_k = $chars[$k];
                        $c_i = $chars[$k-1];
                        if (array_key_exists($c_k, $T[$c_i])) {
                            $T = &$T[$c_i];
                            if ($T[$c_k]['isEnd'] == 1) {       //如果为最后一个匹配规则,结束循环，返回匹配标识数
                                $R[$i] = [
                                    $i => $k
                                ];
                                $i = $k;
                                break;
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        return $replace_array;
    }

    //当redis 缓存不存在时,读取数据库中敏感词到字典树和redis缓存中
    public function importBadWords($words) {
        $this->tree = [];
        foreach ($words as $word) {
            $this->insert($word);
        }
        return $this->tree;
    }
}