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
    private $tree = [];

    public function insert($word)
    {
        $chars = getChars($word);
        $count = count($chars);
        $T = &$this->tree;
        for ($i = 0; $i < $count; $i++) {
            $c = $chars[$i];
            if (!array_key_exists($c, $T)) {
                $T[$c] = ['isEnd' =>0];   //插入新字符，关联数组
            }
            if($i == $count - 1) {
                $T[$c]['isEnd'] = 1;
            }
            $T = &$T[$c];
        }
    }

    public function remove($utf8_str)
    {
        $chars = getChars($utf8_str);
        $chars[] = null;
        if ($this->_find($chars)) {    //先保证此串在树中
            $chars[] = null;
            $count = count($chars);
            $T = &$this->tree;
            for ($i = 0; $i < $count; $i++) {
                $char = $chars[$i];
                if (count($T[$char]) == 1) {     //表明仅有此串
                    unset($T[$char]);
                    return;
                }
                $T = &$T[$char];
            }
        }
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
        $badWords = '';
        $this->tree = $tree;
        $chars = getChars($txt);
        $len = count($chars);
        $T = &$this->tree;
        for ($i = 0; $i < $len; $i++) {
            $char = $chars[$i];
            if (array_key_exists($char, $T)) {//存在，则判断是否为最后一个
                $badWords .= $char;
                if ($T[$char]['isEnd'] == 1) {//如果为最后一个匹配规则,结束循环
                    return $badWords;
                } else {
                    for ($k = $i+1; $k < $len; $k++) {
                        $c_k = $chars[$k];
                        $c_i = $chars[$k-1];
                        if (array_key_exists($c_k, $T[$c_i])) {
                            $badWords .= $c_k;
                            $T = &$T[$c_i];
                            if ($T[$c_k]['isEnd'] == 1) {//如果为最后一个匹配规则,结束循环，返回匹配标识数
                                return $badWords;
                            }
                        } else {
                            $badWords = '';
                            break;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function replace($txt)
    {
        $replace_array = [];
        $R = &$replace_array;
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