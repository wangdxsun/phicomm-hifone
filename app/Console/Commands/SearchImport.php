<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/18
 * Time: 23:01
 */

namespace Hifone\Console\Commands;

use Carbon\Carbon;
use Hifone\Models\Answer;
use Hifone\Models\Question;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Illuminate\Console\Command;
use ES;

class SearchImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:import {type?} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Data into ElasticSearch';

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
     */
    public function handle()
    {
        $type = ucfirst($this->argument('type'));
        if ($type) {
            $methodName = 'import'.$type;
            $id = $this->option('id');
            if (method_exists($this, $methodName)) {
                $this->$methodName($id);
            } else {
                $this->error("method $methodName doesn't exist");
            }
        } else {
            $start = Carbon::now();
            $this->line('Delete Indices...');
            Thread::deleteIndex();
            $this->line('Create Indices...');
            Thread::createIndex();
            User::putMapping();
            $this->line('Import Users...');
            $bar = $this->output->createProgressBar(ceil(User::count()/1000));
            User::chunk(1000, function ($users) use ($bar) {
                $users->addToIndex();
                $bar->advance();
            });
            $this->info("\r\nImport Users into ElasticSearch Successfully!");
            $this->line('Import Threads...');
            $bar = $this->output->createProgressBar(ceil(Thread::visible()->count()/200));
            Thread::visible()->chunk(200, function ($threads) use ($bar) {
                foreach ($threads as $thread) {
                    $thread->body = strip_tags($thread->body);
                }
                $threads->addToIndex();
                $bar->advance();
            });
            $this->info("\r\nImport Threads into ElasticSearch Successfully!");
            $end = Carbon::now();
            $this->line('共计用时：'.$end->diffInSeconds($start).'秒');
        }
    }

    private function importUser($id = null)
    {
        if ($id) {
            $user = User::find($id);
            if (!$user) {
                $this->error("User $id does't exist");
            }
            try {
                $user->removeFromIndex();
                $user->addToIndex();
                $this->info("Import user $id into ElasticSearch Successfully");
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            return;
        }
        User::chunk(1000, function ($users) {
            foreach ($users as $user) {
                try {
                    $user->removeFromIndex();
                } catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                }
            }
            $users->addToIndex();
        });

        $this->info("\r\nImport Users into ElasticSearch Successfully");
    }

    private function importThread($id = null)
    {
        if ($id) {
            $thread = Thread::find($id);
            if (!$thread) {
                $this->error("Thread $id does't exist");
            }
            try {
                $thread->removeFromIndex();
                $thread->body = strip_tags($thread->body);
                $thread->addToIndex();
                $this->info("Import thread $id into ElasticSearch Successfully");
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            return;
        }
        Thread::visible()->chunk(1000, function ($threads) {
            foreach ($threads as $thread) {
                try {
                    $thread->body = strip_tags($thread->body);
                    $thread->removeFromIndex();
                } catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                }
            }
            $threads->addToIndex();
        });

        $this->info("\r\nImport Threads into ElasticSearch Successfully");
    }

    private function importQuestion($id = null)
    {
        if ($id) {
            $question = Question::find($id);
            if (!$question) {
                $this->error("Question $id does't exist");
            }
            try {
                $question->removeFromIndex();
                $question->body = strip_tags($question->body);
                $question->addToIndex();
                $this->info("Import question $id into ElasticSearch Successfully");
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            return;
        }
        Question::visible()->chunk(1000, function ($questions) {
            foreach ($questions as $question) {
                try {
                    $question->body = strip_tags($question->body);
                    $question->removeFromIndex();
                } catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                }
            }
            $questions->addToIndex();
        });

        $this->info("\r\nImport Questions into ElasticSearch Successfully");
    }

    private function importAnswer($id = null)
    {
        if ($id) {
            $question = Answer::find($id);
            if (!$question) {
                $this->error("Answer $id does't exist");
            }
            try {
                $question->removeFromIndex();
                $question->body = strip_tags($question->body);
                $question->addToIndex();
                $this->info("Import answer $id into ElasticSearch Successfully");
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
        } else {
            Answer::with('question')->chunk(100, function ($answers) {
                foreach ($answers as $answer) {
                    try {
                        $answer->body = strip_tags($answer->body);
                        $answer->question->body = strip_tags($answer->question->body);
                        $answer->removeFromIndex();
                    } catch (\Exception $exception) {
                        $this->error($exception->getMessage());
                    }
                }
                $answers->addToIndex();
            });

            $this->info("\r\nImport Answers into ElasticSearch Successfully");
        }
    }
}