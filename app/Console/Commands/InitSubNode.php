<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/18
 * Time: 23:01
 */

namespace Hifone\Console\Commands;


use Hifone\Models\Thread;
use Illuminate\Console\Command;

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
        Thread::where('sub_node_id', 0)->chunk(1000, function ($threads) {
            foreach ($threads as $thread) {
                $subNode = $thread->node->subNodes()->first();
                $thread->update(['sub_node_id' => $subNode->id]);
                $thread->save();
            }
        });

    }
}