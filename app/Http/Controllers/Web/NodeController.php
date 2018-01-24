<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\NodeBll;
use Hifone\Models\Node;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;

class NodeController extends WebController
{
    public function index()
    {
        $nodes = Node::orderBy('order')->has('subNodes')->limit(4)->get();
        $nodes->splice(4);
        return $nodes;
    }

    /**
     * 版块列表(含分类）
     * @param NodeBll $nodeBll
     * @return mixed
     */
    public function sections(NodeBll $nodeBll)
    {
        $sections = $nodeBll->sections();
        return $sections;
    }

    /**
     * 发帖选择子版块
     * @param NodeBll $nodeBll
     * @return mixed
     */
    public function subNodes(NodeBll $nodeBll)
    {
        $sections = $nodeBll->subNodes();
        return $sections;
    }

    /**
     * 版块详情
     * @param Node $node
     * @param NodeBll $nodeBll
     * @return Node
     */
    public function show(Node $node, NodeBll $nodeBll)
    {
        $node = $nodeBll->show($node, $nodeBll);
        return $node;
    }

    /**
     * 版块内按子版块筛选
     * @param SubNode $subNode
     * @param NodeBll $nodeBll
     * @return Node
     */
    public function showOfSubNode(SubNode $subNode, NodeBll $nodeBll)
    {
        $node = $nodeBll->showOfSubNode($subNode, $nodeBll);
        return $node;
    }

    /**
     * 版块热帖推荐
     * @param Node $node
     */
    public function recommendThreadsOfNode(Node $node)
    {
        $threads = Thread::visible()->ofNode($node)->hot()->limit(5)->get();

        return $threads;
    }
}