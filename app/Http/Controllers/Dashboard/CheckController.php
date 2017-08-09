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

        $post = '我色色男';
        $tree = $trieTree->importBadWords(['色色', '色色男', '色色女']);
        $res = $trieTree->contain($post, $tree);

        dd($res);
    }
}
