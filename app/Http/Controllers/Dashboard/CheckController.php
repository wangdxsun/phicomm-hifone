<?php

namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Thread;
use Hifone\Models\User;

class CheckController extends  Controller
{
    public function check() {
//        $users = User::get(['id', 'username', 'follower_count', 'avatar_url', 'role']);
//        foreach ($users as $user) {
//            unset($user['roles']);
//        }
//        $users->addToIndex();
//
//        $threads = Thread::visible()->get(['id', 'title', 'body', 'user_id', 'node_id', 'created_at', 'view_count', 'reply_count']);
//        foreach ($threads as $thread) {
//            $thread->body = strip_tags($thread->body);
//        }
//        $threads->addToIndex();

        $threads = Thread::elasticSearch('规定');

        return $threads->load(['user', 'node']);
    }
}
