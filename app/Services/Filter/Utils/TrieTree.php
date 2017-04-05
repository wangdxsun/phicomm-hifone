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

    private $tree = array();

    public function insert($utf8_str)
    {
        $chars = get_chars($utf8_str);
        $chars[] = null;    //串结尾字符
        $count = count($chars);
        $T = &$this->tree;
        for ($i = 0; $i < $count-1; $i++) {
            $c = $chars[$i];
            if (!array_key_exists($c, $T)) {
                    $T[$c] = array('isEnd' =>0);   //插入新字符，关联数组
            }
            if($i == $count-2){
                $T[$c]['isEnd'] = 1;
            }
            $T = &$T[$c];
        }
    }
  /*  public function insert($utf8_str){
        $chars = get_chars($utf8_str);
        $chars[] = null;	//串结尾字符
        $count = count($chars);
        $T = &$this->tree;
        for($i = 0;$i < $count;$i++){
            $c = $chars[$i];
            if(!array_key_exists($c, $T)){
                $T[$c] = array();	//插入新字符，关联数组
            }
            $T = &$T[$c];
        }
    }*/
    public function remove($utf8_str)
    {
        $chars = get_chars($utf8_str);
        $chars[] = null;
        if ($this->_find($chars)) {    //先保证此串在树中
            $chars[] = null;
            $count = count($chars);
            $T = &$this->tree;
            for ($i = 0; $i < $count; $i++) {
                $c = $chars[$i];
                if (count($T[$c]) == 1) {     //表明仅有此串
                    unset($T[$c]);
                    return;
                }
                $T = &$T[$c];
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

    public function find($utf8_str)
    {
        $chars = get_chars($utf8_str);
        $chars[] = null;
        return $this->_find($chars);
    }

  /*  public function contain($utf8_str, $do_count = 0)
    {
        $chars = get_chars($utf8_str);
        $chars[] = null;
        $len = count($chars);
        $Tree = &$this->tree;
        $count = 0;
        for ($i = 0; $i < $len; $i++) {
            $c = $chars[$i];
            if (array_key_exists($c, $Tree)) {    //起始字符匹配
                $T = &$Tree[$c];
                for ($j = $i + 1; $j < $len; $j++) {
                    $c = $chars[$j];
                    if (array_key_exists(null, $T)) {
                        if ($do_count) {
                            $count++;
                        } else {
                            return true;
                        }
                    }
                    if (!array_key_exists($c, $T)) {
                        break;
                    }
                    $T = &$T[$c];
                }
            }
        }
        if ($do_count) {
            return $count;
        } else {
            return false;
        }
    }*/
    public function contain($txt, $do_count = 0)
    {
        $flag = 0;    //敏感词结束标识位：用于敏感词只有1位的情况
        $chars = get_chars($txt);
        $len = count($chars);
        $Tree = &$this->tree;
        for($i = 0; $i < $len; $i++){
            $c = $chars[$i];
            if(array_key_exists($c, $Tree)){
                //存在，则判断是否为最后一个
                if($Tree[$c]['isEnd'] == 1){       //如果为最后一个匹配规则,结束循环，返回匹配标识数
                    $flag = 1;
                    break;
                }else{
                    $T = &$Tree;
                    for($k = $i+1; $k < $len; $k++){
                        $c_k = $chars[$k];
                        $c_i = $chars[$k-1];
                        if(array_key_exists($c_k, $T[$c_i])){
                            $T = &$T[$c_i];
                            if($T[$c_k]['isEnd'] == 1) {       //如果为最后一个匹配规则,结束循环，返回匹配标识数
                                $flag = 1;
                                $i = $k+1;
                                break;
                            }
                        }else{
                            break;
                        }
                    }
                }
            }
        }
        return $flag;
    }
    public function replace($txt)
    {
        $replace_array = [];
        $R = &$replace_array;
        $chars = get_chars($txt);
        $len = count($chars);
        $Tree = &$this->tree;
        for($i = 0; $i < $len; $i++){
            $c = $chars[$i];
            if(array_key_exists($c, $Tree)){     //存在，则判断是否为最后一个
                if($Tree[$c]['isEnd'] == 1){       //如果为最后一个匹配规则,结束循环，返回匹配标识数
                    $R[$i] = [
                        $i => $i
                    ];
                }else{
                    $T = &$Tree;
                    for($k = $i+1; $k < $len; $k++){
                        $c_k = $chars[$k];
                        $c_i = $chars[$k-1];
                        if(array_key_exists($c_k, $T[$c_i])){
                            $T = &$T[$c_i];
                            if($T[$c_k]['isEnd'] == 1) {       //如果为最后一个匹配规则,结束循环，返回匹配标识数
                                $R[$i] = [
                                    $i => $k
                                ];
                                $i = $k;
                                break;
                            }
                        }else{
                            break;
                        }
                    }
                }
            }
        }
        return $replace_array;
    }

    //更新敏感词列表
    public function export()
    {
        return serialize($this->tree);
    }
    //初始化敏感词树
    public function import($str)
    {
        $this->tree = unserialize($str);
    }

    //当redis 缓存不存在时,读取数据库中敏感词到字典树和redis缓存中
    public function  importBadWords($data){
        $this->tree = array();
        foreach ($data as $v){

            $this->insert($v);
        }
        return $this->tree;
    }
}