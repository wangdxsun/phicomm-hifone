<?php

namespace Hifone\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Node;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Illuminate\Support\Str;

class CheckController extends  Controller
{
    public function check() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        Thread::where('sub_node_id', 0)->with('node')->chunk(1000, function ($threads) {
            foreach ($threads as $thread) {
//                $node = Node::where('name','资讯杂谈')->first();
//                dd( $subNode = $node->subNodes()->where('name','趣味杂谈')->first());
                if ($thread->node->id == 38) {
                    //产品体验中的帖子
                    if (Str::contains($thread->title,['体脂称','S7','净水','净化','空立方','M1','手环','W1','手表','小龙'])) {
                        $node = Node::where('name','资讯杂谈')->first();
                        //dd($node);
                        $subNode = $node->subNodes()->where('name','趣味杂谈')->first();
                    } elseif (Str::contains($thread->title,'K2')) {
                        $node = Node::where('name','K2系列')->first();
                       // dd($node);
                        $subNode = $node->subNodes()->where('name','求助讨论')->first();
                    } elseif (Str::contains($thread->title,'K3')) {
                        $node = Node::where('name','K3系列')->first();
                        //dd($node);
                        $subNode = $node->subNodes()->where('name','求助讨论')->first();
                    } elseif (Str::contains($thread->body,['体脂称','S7','净水','净化','空立方','M1','手环','W1','手表','小龙'])) {
                        $node = Node::where('name','资讯杂谈')->first();
                        //($node);
                        $subNode = $node->subNodes()->where('name','趣味杂谈')->first();
                    } elseif (Str::contains($thread->body,'K2')) {
                        $node = Node::where('name','K2系列')->first();
//                        dd($node);
                        $subNode = $node->subNodes()->where('name','求助讨论')->first();
                    } elseif (Str::contains($thread->body,'K3')) {
                        $node = Node::where('name','K3系列')->first();
//                        dd($node);
                        $subNode = $node->subNodes()->where('name','求助讨论')->first();
                    } else {
                        $node = Node::where('name','其他系列')->first();
//                        dd($node);
                        $subNode = $node->subNodes->where('name','求助讨论')->first();
//                        dd('here');
                    }
                } elseif ($thread->node->id == 37 ) {
                    //公告活动中的帖子
                    if (Str::contains($thread->title,['调研','问卷','调查','内测','招募','公测','试用'])) {
                        $node = Node::where('name','公告活动')->first();
//                        dd($node);
                        $subNode = $node->subNodes()->where('name','产品调研')->first();
                    } elseif (Str::contains($thread->title,['商城','开售','预约'])) {
                        $node = Node::where('name','公告活动')->first();
//                        dd($node);
                        $subNode = $node->subNodes()->where('name','商城快讯')->first();
                    } elseif (Str::contains($thread->body,['调研','问卷','调查','内测','招募','公测','试用'])) {
                        $node = Node::where('name','公告活动')->first();
//                        dd($node);
                        $subNode = $node->subNodes()->where('name','产品调研')->first();
                    } elseif (Str::contains($thread->title,['商城','开售','预约'])) {
                        $node = Node::where('name', '公告活动')->first();
//                        dd($node);
                        $subNode = $node->subNodes()->where('name', '商城快讯')->first();
                    } else {
                        $node = Node::where('name', '公告活动')->first();
//                        dd($node);
                        $subNode = $node->subNodes()->where('name','官方公告')->first();
                    }
                } else {
                    //技术资讯中的帖子
                    $node = Node::where('name','资讯杂谈')->first();
//                    dd($node);
                    $subNode = $node->subNodes()->where('name','快讯分享')->first();
                }
                $thread->update(['sub_node_id' => $subNode->id]);
                $thread->save();
            }
        });
        echo 'Init SubNode successfullt';
        return;
    }
}
