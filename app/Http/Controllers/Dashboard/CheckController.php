<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Services\Filter\Utils\TrieTree;
use Hifone\Services\Filter\WordsFilter;

class CheckController extends  Controller
{
    public function check(WordsFilter $wordsFilter) {
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

        $post = '2016年彩图集合';
        $start = microtime(true) * 1000;
        $res = $wordsFilter->filterWord($post);
        $end = microtime(true) * 1000;
//        \Cache::flush();

        return [
            'res' => $res,
            'time' => $end - $start,
            'tree' => \Cache::get('words'),
            'count' => count(\Cache::get('words')),
        ];


//        $oldTree = \Cache::get('words', ['fuck']);
//        $trieTree->tree = $oldTree;
//
//        $newTree = $trieTree->insert('这个肯定不是敏感词');
//        $newTree2 = $trieTree->remove('fuck');
//        dd($oldTree, $newTree, $newTree2);
    }
}
