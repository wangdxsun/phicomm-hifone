<?php

namespace Hifone\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Node;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Illuminate\Support\Str;

class CheckController extends  Controller
{
    public function check() {
        $nodes = Node::orderBy('order')->with('subNodes')->get();
        foreach ($nodes as $node) {
            $node->update(['thread_count' => $node->threads()->visible()->count()]);
            foreach ($node->subNodes as $subNode) {
                $subNode->update(['thread_count' => $subNode->threads()->visible()->count()]);
                foreach ($subNode->threads as $thread) {
                    $subNode->reply_count = $subNode->reply_count + $thread->reply_count;
                }
                $subNode->update(['reply_count' => $subNode->reply_count]);
                $node->reply_count = $node->reply_count + $subNode->reply_count;
            }
            $node->update(['reply_count' =>$node->reply_count ]);
        }
        echo 'Init SubNode successful';
        return;
    }
}
