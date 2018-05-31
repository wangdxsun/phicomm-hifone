<?php
/**
 * Created by PhpStorm.
 * User: daoxin.wang
 * Date: 2018/5/30
 * Time: 10:40
 */

namespace Hifone\Console\Commands;

use Hifone\Events\Adopt\AdopeAsSoonAsPossibleEvent;
use Hifone\Exceptions\Handler;
use Hifone\Models\Question;
use Illuminate\Console\Command;

class RemindAdopt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind:adopt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remind user to adopt answer as soon as possible.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(Handler $handler)
    {
        Question::visible()->remindAdopt()->chunk(100, function ($questions) use ($handler) {
            foreach ($questions as $question) {
                try {
                    event(new AdopeAsSoonAsPossibleEvent($question));
                } catch (\Exception $e) {
                    \Log::info('RemindAdopt:question', $question->toArray());
                    $handler->report($e);
                }
            }
        });
    }
}