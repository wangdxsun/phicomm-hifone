<?php

namespace Hifone\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Thread;
use Hifone\Models\User;

class CheckController extends  Controller
{
    public function check() {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        User::chunk(1000, function ($users) {
            foreach ($users as $user) {
                unset($user['roles']);
            }
            $users->addToIndex();
        });

        Thread::visible()->chunk(1000, function ($threads) {
            $threads = Thread::visible()->get();
            foreach ($threads as $thread) {
                $thread->body = strip_tags($thread->body);
            }
            $threads->addToIndex();
        });

        $threads = Thread::elasticSearch('规定');

        return $threads->load(['user', 'node']);
    }
}
