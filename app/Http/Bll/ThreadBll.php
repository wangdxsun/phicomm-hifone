<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 16:11
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Thread\AddThreadCommand;
use Hifone\Models\Thread;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use Input;
use Config;
use Auth;

class ThreadBll extends BaseBll
{
    public function getThreads()
    {
        (new CommonBll())->checkLogin();

        $repository = app('repository');
        $repository->pushCriteria(new Filter(Input::query('filter')));
        $repository->pushCriteria(new Search(Input::query('q')));
        $threads = $repository->model(Thread::class)->getThreadList(Config::get('setting.threads_per_page', 15));

        return $threads;
    }

    public function createThread()
    {
        $threadData = Input::get('thread');
        $node_id = isset($threadData['node_id']) ? $threadData['node_id'] : null;
        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';

        dispatch(new AddThreadCommand(
            $threadData['title'],
            $threadData['body'],
            Auth::id(),
            $node_id,
            $tags
        ));
    }
}