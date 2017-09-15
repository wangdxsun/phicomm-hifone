<?php

namespace Hifone\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Thread;
use Hifone\Models\User;

class CheckController extends  Controller
{
    public function check() {
        $users = User::get();
        foreach ($users as $user) {
            unset($user['roles']);
        }
        $users->addToIndex();

        $threads = Thread::visible()->get();
        foreach ($threads as $thread) {
            $thread->body = strip_tags($thread->body);
        }
        $threads->addToIndex();

        $threads = Thread::elasticSearch('规定');

        return $threads->load(['user', 'node']);
    }
}
