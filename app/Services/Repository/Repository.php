<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Services\Repository;

use Hifone\Repositories\Eloquent\Repository as BaseRepository;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Config;

class Repository extends BaseRepository
{
    protected $modelName;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function model($modelName = null)
    {
        $this->modelName = $modelName;
        $this->resetScope();
        $this->makeModel();

        return $this;
    }

    public function makeModel()
    {
        $model = $this->app->make($this->modelName);
        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    public function getThreadList($limit = null)
    {
        $limit = $limit ?: Config::get('setting.threads_per_page', 15);
        $this->applyCriteria();

        return $this->model->visible()->with('user', 'node', 'lastReplyUser')->paginate($limit);
    }
}
