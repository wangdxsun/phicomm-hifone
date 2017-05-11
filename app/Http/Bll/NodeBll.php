<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/5
 * Time: 15:59
 */

namespace Hifone\Http\Bll;

use Hifone\Models\Node;
use Hifone\Models\Thread;
use Hifone\Repositories\Criteria\Thread\BelongsToNode;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use Input;

class NodeBll extends BaseBll
{
    public function threads($node, $filter = null)
    {
        $repository = app('repository');
        $repository->pushCriteria(new Search(Input::query('q')));
        $repository->pushCriteria(new BelongsToNode($node->id));
        $repository->pushCriteria(new Filter($filter));

        $threads = $repository->model(Thread::class)->getThreadList();

        return $threads;
    }

    public function recentThreads(Node $node)
    {
        $threads = Thread::visible()->ofNode($node)->recent()->with(['user'])->paginate(15);

        return $threads;
    }

    public function hotThreads(Node $node)
    {
        $threads = Thread::visible()->ofNode($node)->pinAndRecentReply()->with(['user'])->paginate(15);

        return $threads;
    }
}