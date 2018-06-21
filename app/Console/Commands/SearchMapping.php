<?php

namespace Hifone\Console\Commands;

use Hifone\Models\Answer;
use Hifone\Models\Question;
use Illuminate\Console\Command;

class SearchMapping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:putmapping {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put mapping to elasticsearch';

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
        $type = ucfirst($this->argument('type'));
        $methodName = 'put'.$type;
        if (method_exists($this, $methodName)) {
            $this->$methodName();
        } else {
            $this->error("method $methodName doesn't exist");
        }
    }

    private function putQuestion()
    {
        Question::putMapping();
    }

    private function putAnswer()
    {
        Answer::putMapping();
    }
}
