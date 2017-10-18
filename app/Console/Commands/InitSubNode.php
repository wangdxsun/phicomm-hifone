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
        $nodes = Node::orderBy('order')->with('subNodes')->get();
        foreach ($nodes as $node) {
            $node->reply_count = 0;
            $node->update(['thread_count' => $node->threads()->visible()->count()]);
            foreach ($node->subNodes as $subNode) {
                $subNode->update(['thread_count' => $subNode->threads()->visible()->count()]);
                $subNode->reply_count = 0;
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