<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/18
 * Time: 23:01
 */

namespace Hifone\Console\Commands;


use Hifone\Events\Chat\NewMessageEvent;
use Illuminate\Console\Command;

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'echo {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Broadcast a message';

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
        $message = $this->argument('message');
        event(new NewMessageEvent($message));
    }
}