<?php
namespace Hifone\Console\Commands;

use Carbon\Carbon;
use Hifone\Models\Node;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Illuminate\Console\Command;
use Hifone\Models\Rank;

class InitNodesThreadAndReplyCount extends Command
{
    protected $signature = 'init:count';

    protected $description = "init nodes and subNodes' threads and replies count ";

    public function __construct()
    {
        parent::__construct();
    }

    //初始化主板块和子版块中的帖子和回复计数
    public function handle()
    {
        $nodes = Node::orderBy('order')->with('subNodes')->get();
        foreach ($nodes as $node) {
            $node->update([
                'thread_count' => $node->threads()->visibleAndDeleted()->count(),
                'reply_count'  => $node->threads()->visibleAndDeleted()->count(),
            ]);
            foreach ($node->subNodes as $subNode) {
                $subNode->update([
                    'thread_count' => $subNode->threads()->visibleAndDeleted()->count(),
                    'reply_count'  => $node->threads()->visibleAndDeleted()->count(),
                ]);
            }
            
        }
    }
}