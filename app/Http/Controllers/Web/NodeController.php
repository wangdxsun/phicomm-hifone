<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Http\Bll\NodeBll;
use Hifone\Models\Node;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;

class NodeController extends WebController
{
    private $nodeBll;

    public function __construct(NodeBll $nodeBll)
    {
        $this->nodeBll = $nodeBll;
        parent::__construct();
    }

    public function index()
    {
        $nodes = Node::orderBy('order')->has('subNodes')->limit(6)->get();
        return $nodes;
    }

    /**
     * 版块列表(含分类）
     * @return mixed
     */
    public function sections()
    {
        $sections = $this->nodeBll->sections();
        return $sections;
    }

    /**
     * 发帖选择子版块
     * @return mixed
     */
    public function subNodes()
    {
        $sections = $this->nodeBll->subNodes();
        return $sections;
    }

    /**
     * 版块详情
     * @param Node $node
     * @return Node
     */
    public function show(Node $node)
    {
        $node = $this->nodeBll->show($node);
        return $node;
    }

    /**
     * 版块内按子版块筛选
     * @param SubNode $subNode
     * @return Node
     */
    public function showOfSubNode(SubNode $subNode, $sort = 'all')
    {
        switch ($sort) {
            case 'hot'://子版块热门帖子列表
                $threads = $this->nodeBll->hotThreadsOfSubNode($subNode);
                break;
            case 'recent'://子版块最新帖子列表
                $threads = $this->nodeBll->recentThreadsOfSubNode($subNode);
                break;
            case 'excellent'://子版块精华帖子列表
                $threads = $this->nodeBll->excellentThreadsOfSubNode($subNode);
                break;
            default :
                $subNode = $this->nodeBll->showOfSubNode($subNode);
                return $subNode;
        }

        return $threads;
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

    public function hot(Node $node)
    {
        $threads = $this->nodeBll->hotThreadsOfNode($node);

        return $threads;
    }

    public function recent(Node $node)
    {
        $threads = $this->nodeBll->recentThreadsOfNode($node);

        return $threads;
    }

    //精华帖子，按照加精时间和发表时间排序
    public function excellent(Node $node)
    {
        $threads = $this->nodeBll->excellentThreadsOfNode($node);

        return $threads;
    }
}