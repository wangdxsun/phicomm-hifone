<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/5
 * Time: 15:59
 */

namespace Hifone\Http\Bll;

use Hifone\Models\Node;
use Hifone\Models\Section;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Repositories\Criteria\Thread\BelongsToNode;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use Input;
use Auth;

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

    public function recentThreadsOfNode(Node $node)
    {
        $threads = Thread::visible()->ofNode($node)->recentEdit()->with(['user', 'subNode'])->paginate();

        return $threads;
    }

    public function hotThreadsOfNode(Node $node)
    {
        $threads = Thread::visible()->ofNode($node)->hot()->with(['user', 'subNode'])->paginate();

        return $threads;
    }

    //主版块中的精华帖子
    public function excellentThreadsOfNode(Node $node)
    {
        $threads = Thread::visible()->ofNode($node)->excellent()->with(['user', 'subNode'])->paginate();

        return $threads;
    }

    public function sections()
    {
        $sections = Section::orderBy('order')->with(['nodes.subNodes', 'nodes' => function ($query) {
            $query->has('subNodes');
        }])->whereHas('nodes', function ($query) {
            $query->has('subNodes');
        })->get();

        return $sections;
    }

    public function subNodes()
    {
        //除去无子版块的版块信息,同时判断用户身份决定是否显示公告活动等主版块
        $sections = Section::orderBy('order')->with(['nodes.subNodes', 'nodes' => function ($query) {
            $query->show()->has('subNodes');//管理员和普通用户都不能在隐藏的版块发帖
        }])->has('nodes')->get();

        return $sections;
    }

    public function subNodesInFeedback()
    {
        //意见反馈处显示的子版块
        $nodes = Node::orderBy('order')->with(['subNodes' => function ($query) {
            $query->feedback();
        }])->get()->filter(function ($node) {
            return $node->subNodes->count() > 0;
        })->values();

        return $nodes;
    }

    //支持用户反馈的主版块
    public function nodesInFeedback()
    {
        //意见反馈处显示的子版块
        $nodes = Node::orderBy('order')->feedback()->show()->has('subNodes')->get(['id', 'name','feedback_thread_id']);

        return $nodes;
    }

    public function show(Node $node)
    {
        $hot = $this->hotThreadsOfNode($node);
        $recent = $this->recentThreadsOfNode($node);
        $excellent = $this->excellentThreadsOfNode($node);
        $moderators = $node->moderators()->get();
        $praModerators = $node->praModerators()->get();
        $subNodes = $node->subNodes()->select(['name', 'id'])->get();

        $node['hot'] = $hot;
        $node['recent'] = $recent;
        $node['excellent'] = $excellent;
        $node['moderators'] = [];
        $node['praModerators'] = $praModerators;
        $node['subNodes'] = $subNodes;
        $node['followed'] = User::hasFollowNode($node);

        return $node;
    }

    public function recentThreadsOfSubNode(SubNode $subNode)
    {
        $threads = Thread::visible()->ofSubNode($subNode)->recentEdit()->with(['user', 'subNode'])->paginate();

        return $threads;
    }

    public function hotThreadsOfSubNode(SubNode $subNode)
    {
        $threads = Thread::visible()->ofSubNode($subNode)->hot()->with(['user', 'subNode'])->paginate();

        return $threads;
    }

    //子版块中的精华帖子
    public function excellentThreadsOfSubNode(SubNode $subNode)
    {
        $threads = Thread::visible()->ofSubNode($subNode)->excellent()->with(['user', 'subNode'])->paginate();

        return $threads;
    }

    public function showOfSubNode(SubNode $subNode)
    {
        $hot = $this->hotThreadsOfSubNode($subNode);
        $recent = $this->recentThreadsOfSubNode($subNode);
        $excellent = $this->excellentThreadsOfSubNode($subNode);
        $moderators = $subNode->node->moderators()->get();

        $subNode['hot'] = $hot;
        $subNode['recent'] = $recent;
        $subNode['excellent'] = $excellent;
        $subNode['moderators'] = $moderators;

        return $subNode;
    }

}