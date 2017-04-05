<?php
/**
 * Created by PhpStorm.
 * User: wei07.wang
 * Date: 2017/3/28
 * Time: 16:02
 */
function test(){
    return true;
}

function get_chars($utf8_str)
{
    $s = $utf8_str;
    $len = strlen($s);
    if ($len == 0) return array();
    $chars = array();
    for ($i = 0; $i < $len; $i++) {
        $c = $s[$i];
        $n = ord($c);
        if (($n >> 7) == 0) {       //0xxx xxxx, asci, single
            $chars[] = $c;
        } else if (($n >> 4) == 15) {     //1111 xxxx, first in four char
            if ($i < $len - 3) {
                $chars[] = $c . $s[$i + 1] . $s[$i + 2] . $s[$i + 3];
                $i += 3;
            }
        } else if (($n >> 5) == 7) {  //111x xxxx, first in three char
            if ($i < $len - 2) {
                $chars[] = $c . $s[$i + 1] . $s[$i + 2];
                $i += 2;
            }
        } else if (($n >> 6) == 3) {  //11xx xxxx, first in two char
            if ($i < $len - 1) {
                $chars[] = $c . $s[$i + 1];
                $i++;
            }
        }
    }
    return $chars;
}