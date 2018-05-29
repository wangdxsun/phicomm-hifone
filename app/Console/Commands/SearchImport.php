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
            Question::putMapping();
            Answer::putMapping();

            $this->line('Import Users...');
            $bar = $this->output->createProgressBar(ceil(User::count()/1000));
            User::chunk(1000, function ($users) use ($bar) {
                $users->addToIndex();
                $bar->advance();
            });
            $this->info("\r\nImport Users into ElasticSearch Successfully!");

            $this->line('Import Threads...');
            $bar = $this->output->createProgressBar(ceil(Thread::count()/200));
            Thread::chunk(200, function ($threads) use ($bar) {
                foreach ($threads as $thread) {
                    $thread->body = strip_tags($thread->body);
                }
                $threads->addToIndex();
                $bar->advance();
            });
            $this->info("\r\nImport Threads into ElasticSearch Successfully!");

            $this->line('Import Questions...');
            $bar = $this->output->createProgressBar(ceil(Question::count()/200));
            Question::chunk(200, function ($questions) use ($bar) {
                foreach ($questions as $question) {
                    $question->body = strip_tags($question->body);
                }
                $questions->addToIndex();
                $bar->advance();
            });
            $this->info("\r\nImport Questions into ElasticSearch Successfully!");

            $this->line('Import Answers...');
            $bar = $this->output->createProgressBar(ceil(Answer::count()/200));
            Answer::chunk(200, function ($answers) use ($bar) {
                foreach ($answers as $answer) {
                    $answer->body = strip_tags($answer->body);
                }
                $answers->addToIndex();
                $bar->advance();
            });
            $this->info("\r\nImport Answers into ElasticSearch Successfully!");

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
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            $user->addToIndex();
            $this->info("Import user $id into ElasticSearch Successfully");
        } else {
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
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            $thread->body = strip_tags($thread->body);
            $thread->addToIndex();
            $this->info("Import thread $id into ElasticSearch Successfully");
        } else {
            Thread::chunk(1000, function ($threads) {
                foreach ($threads as $thread) {
                    try {
                        $thread->removeFromIndex();
                    } catch (\Exception $exception) {
                        $this->error($exception->getMessage());
                    }
                    $thread->body = strip_tags($thread->body);
                }
                $threads->addToIndex();
            });
            $this->info("\r\nImport Threads into ElasticSearch Successfully");
        }
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
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            $question->body = strip_tags($question->body);
            $question->addToIndex();
            $this->info("Import question $id into ElasticSearch Successfully");
        } else {
            Question::chunk(1000, function ($questions) {
                foreach ($questions as $question) {
                    try {
                        $question->removeFromIndex();
                    } catch (\Exception $exception) {
                        $this->error($exception->getMessage());
                    }
                    $question->body = strip_tags($question->body);
                }
                $questions->addToIndex();
            });
            $this->info("\r\nImport Questions into ElasticSearch Successfully");
        }
    }

    private function importAnswer($id = null)
    {
        if ($id) {
            $answer = Answer::find($id);
            if (!$answer) {
                $this->error("Answer $id does't exist");
            }
            try {
                $answer->removeFromIndex();
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            $answer->body = strip_tags($answer->body);
            $answer->question->body = strip_tags($answer->question->body);
            $answer->addToIndex();
            $this->info("Import answer $id into ElasticSearch Successfully");
        } else {
            Answer::chunk(100, function ($answers) {
                foreach ($answers as $answer) {
                    try {
                        $answer->removeFromIndex();
                    } catch (\Exception $exception) {
                        $this->error($exception->getMessage());
                    }
                    $answer->body = strip_tags($answer->body);
                }
                $answers->addToIndex();
            });

            $this->info("\r\nImport Answers into ElasticSearch Successfully");
        }
    }
}