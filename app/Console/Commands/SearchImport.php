<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/18
 * Time: 23:01
 */

namespace Hifone\Console\Commands;

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
    protected $signature = 'search:import {type?}';

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
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $type = $this->argument('type');
        if ($type == 'users') {
            User::chunk(5000, function ($users) {
                $users->removeFromIndex();
            });
            User::rebuildMapping();
            User::chunk(5000, function ($users) {
                foreach ($users as $user) {
                    unset($user['roles']);
                }
                $users->addToIndex();
            });

            echo 'Import Users into ElasticSearch Successfully';
        } elseif ($type == 'threads') {
            Thread::visible()->chunk(1000, function ($threads) {
                $threads->removeFromIndex();
            });
            Thread::rebuildMapping();
            Thread::visible()->chunk(1000, function ($threads) {
                foreach ($threads as $thread) {
                    $thread->body = strip_tags($thread->body);
                }
                $threads->addToIndex();
            });

            echo 'Import Threads into ElasticSearch Successfully';
        } else {
            Thread::deleteIndex();
            Thread::createIndex();
            User::putMapping();
            User::chunk(5000, function ($users) {
                foreach ($users as $user) {
                    unset($user['roles']);
                }
                $users->addToIndex();
            });
            Thread::visible()->chunk(1000, function ($threads) {
                foreach ($threads as $thread) {
                    $thread->body = strip_tags($thread->body);
                }
                $threads->addToIndex();
            });
            echo 'Import Data into ElasticSearch Successfully';
        }
        return;
    }
}