<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/18
 * Time: 23:01
 */

namespace Hifone\Console\Commands;


use Hifone\Models\Node;
use Hifone\Models\Thread;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InitSubNode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:subNode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'init subNode where used to be 0';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        Thread::where('sub_node_id', 0)->with('node')->chunk(1000, function ($threads) {
            foreach ($threads as $thread) {
                if ($thread->node->name == '产品体验') {
                    //产品体验中的帖子
                    if (Str::contains($thread->title,['体脂称','S7','净水','净化','空立方','M1','手环','W1','手表','小龙'])) {
                        $node = Node::where('name','资讯杂谈')->get();
                        $subNode = $node->subNodes->where('name','趣味杂谈')->get();
                    } elseif (Str::contains($thread->title,'K2')) {
                        $node = Node::where('name','K2系列')->get();
                        $subNode = $node->subNodes->where('name','求助讨论')->get();
                    } elseif (Str::contains($thread->title,'K3')) {
                        $node = Node::where('name','K3系列')->get();
                        $subNode = $node->subNodes->where('name','求助讨论')->get();
                    } elseif (Str::contains($thread->body,['体脂称','S7','净水','净化','空立方','M1','手环','W1','手表','小龙'])) {
                        $node = Node::where('name','资讯杂谈')->get();
                        $subNode = $node->subNodes->where('name','趣味杂谈')->get();
                    } elseif (Str::contains($thread->body,'K2')) {
                        $node = Node::where('name','K2系列')->get();
                        $subNode = $node->subNodes->where('name','求助讨论')->get();
                    } elseif (Str::contains($thread->body,'K3')) {
                        $node = Node::where('name','K3系列')->get();
                        $subNode = $node->subNodes->where('name','求助讨论')->get();
                    } else {
                        $node = Node::where('name','其他系列');
                        $subNode = $node->subNodes->where('name','求助讨论')->get();
                    }
                } elseif ($thread->node->name == '公告活动' ) {
                    //公告活动中的帖子
                    if (Str::contains($thread->title,['调研','问卷','调查','内测','招募','公测','试用'])) {
                        $node = Node::where('name','公告活动')->get();
                        $subNode = $node->subNodes->where('name','产品调研')->get();
                    } elseif (Str::contains($thread->title,['商城','开售','预约'])) {
                        $node = Node::where('name','公告活动')->get();
                        $subNode = $node->subNodes->where('name','商城快讯')->get();
                    } elseif (Str::contains($thread->body,['调研','问卷','调查','内测','招募','公测','试用'])) {
                        $node = Node::where('name','公告活动')->get();
                        $subNode = $node->subNodes->where('name','产品调研')->get();
                    } elseif (Str::contains($thread->title,['商城','开售','预约'])) {
                        $node = Node::where('name', '公告活动')->get();
                        $subNode = $node->subNodes->where('name', '商城快讯')->get();
                    } else {
                        $node = Node::where('name', '公告活动')->get();
                        $subNode = $node->subNodes->where('name','官方公告')->get();
                    }
                } else {
                    //技术资讯中的帖子
                    $node = Node::where('name','技术资讯')->get();
                    $subNode = $node->subNodes->where('name','快讯分享');
                }
                $thread->update(['sub_node_id' => $subNode->id]);
                $thread->save();
            }
        });

    }
}