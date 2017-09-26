<?php

namespace Hifone\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Illuminate\Support\Str;

class CheckController extends  Controller
{
    public function check() {
        $str = '我的一个字符串，现在亦喜爱洒洒水啊飒飒按时按时';
        dd(Str::contains($str,['测试','现在']));

//        ini_set('memory_limit', '-1');
//        ini_set('max_execution_time', 0);
//        User::chunk(1000, function ($users) {
//            foreach ($users as $user) {
//                unset($user['roles']);
//            }
//            $users->addToIndex();
//        });
//
//        Thread::visible()->chunk(1000, function ($threads) {
//            $threads = Thread::visible()->get();
//            foreach ($threads as $thread) {
//                $thread->body = strip_tags($thread->body);
//            }
//            $threads->addToIndex();
//        });
//
//        $threads = Thread::elasticSearch('规定');
//
//        return $threads->load(['user', 'node']);
    }
}
