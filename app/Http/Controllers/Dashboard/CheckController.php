<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Services\Filter\Utils\TrieTree;
use Hifone\Services\Filter\WordsFilter;

class CheckController extends  Controller
{
    public function check(WordsFilter $wordsFilter, TrieTree $trieTree) {
//        $posts = Reply::where('body_original', '')->where('body', '<>', '')->limit(3000)->get();
//        $posts = Reply::where('id', 96)->get();
//        $data = [];
//        foreach ($posts as $post) {
//            $start = microtime(true) * 1000;
//            $res = $wordsFilter->filter($post->body);
//            $post->update(['body_original' => $res['type'], 'last_op_reason' => $res['word']]);
//            $end = microtime(true) * 1000;
//            $data[] = [
//                'id' => $post->id,
//                'type' => $res['type'],
//                'word' => $res['word'],
//                'time' => $end - $start,
//                'status' => $post->status,
//                'body' => $post->body,
//            ];
//        }
//        return $data;

//        $post = '<p>无辜的帖子</p>我是一个色色的帖子';
//        $start = microtime(true) * 1000;
//        $res = $wordsFilter->filterWord($post);
//        $end = microtime(true) * 1000;
//        \Cache::flush();

        //测试用例1 全缓存匹配post
        $post = '<p> <img class="face" src="http://hifone1.wdx.dev.phiwifi.com:1885/images/emotion/face-dweiqu.png"><img class="face" src="http://hifone1.wdx.dev.phiwifi.com:1885/images/emotion/face-dwu.png"><img class="face" src="http://hifone1.wdx.dev.phiwifi.com:1885/images/emotion/face-dxiaoku.png"><img class="face" src="http://hifone1.wdx.dev.phiwifi.com:1885/images/emotion/face-dxingxingyan.png"><img class="face" src="http://hifone1.wdx.dev.phiwifi.com:1885/images/emotion/face-dxixi.png"><img class="face" src="http://hifone1.wdx.dev.phiwifi.com:1885/images/emotion/face-dxu.png"><img class="face" src="http://hifone1.wdx.dev.phiwifi.com:1885/images/emotion/face-dyinxian.png"><img class="face" src="http://hifone1.wdx.dev.phiwifi.com:1885/images/emotion/face-dyiwen.png"> </p>';
        $post = 'fuck';
        $res = $wordsFilter->filterWord($post);

        //测试用例2 局部新建字典树匹配post
//        $post = 'abcdefghijk';
//        $tree = $trieTree->importBadWords(['abcf', 'bcd', 'd', 'g']);
//        $res = $trieTree->contain($post, $tree);


        return [
            'res' => $res,
//            'time' => $end - $start,
//            'tree' => \Cache::get('words'),
//            'count' => count(\Cache::get('words')),
        ];
    }
}
