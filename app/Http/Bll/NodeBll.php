<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/5
 * Time: 15:59
 */

namespace Hifone\Http\Bll;

use Hifone\Models\Thread;
use Hifone\Repositories\Criteria\Thread\BelongsToNode;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use Config;
use Input;

class NodeBll extends BaseBll
{
    public function getThreads($node)
    {
        $repository = app('repository');
        $repository->pushCriteria(new Search(Input::query('q')));
        $repository->pushCriteria(new BelongsToNode($node->id));
        $repository->pushCriteria(new Filter('node'));

        $threads = $repository->model(Thread::class)->getThreadList(Config::get('setting.threads_per_page'));

        return $threads;
    }
}