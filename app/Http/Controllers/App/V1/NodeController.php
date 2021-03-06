<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 15:37
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Bll\NodeBll;
use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Node;
use Hifone\Models\SubNode;
use Hifone\Models\User;

class NodeController extends AppController
{
    private $nodeBll;

    public function __construct(NodeBll $nodeBll)
    {
        $this->nodeBll = $nodeBll;
        parent::__construct();
    }

    public function index()
    {
        $nodes = Node::orderBy('order')->limit(4)->get();
        foreach ($nodes as $node) {
            $node['detail_url'] = route('app.node.show', $node->id);
        }

        return $nodes;
    }

    /**
     * 版块列表(含分类）
     * @return mixed
     */
    public function sections()
    {
        $sections = $this->nodeBll->sections();
        foreach ($sections as $section) {
            foreach ($nodes = $section['nodes'] as $node) {
                $node['detail_url'] = route('app.node.show', $node->id);
                $node['followed'] = User::hasFollowNode($node);
            }
        }

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
    public function showOfSubNode(SubNode $subNode)
    {
        $node = $this->nodeBll->showOfSubNode($subNode);

        return $node;
    }

    /**
     * 意见反馈发帖选择子版块
     */
    public function subNodesInFeedback()
    {
        $nodes = $this->nodeBll->subNodesInFeedback();
        return $nodes;
    }

    /**
     * 意见反馈发帖选择主版块
     */
    public function nodesInFeedback()
    {
        $nodes = $this->nodeBll->nodesInFeedback();
        return $nodes;
    }

    //版块热门帖子列表
    public function hot(Node $node)
    {
        $threads = $this->nodeBll->hotThreadsOfNode($node);

        return $threads;
    }

    //版块最新帖子列表
    public function recent(Node $node)
    {
        $threads = $this->nodeBll->recentThreadsOfNode($node);

        return $threads;
    }

    //子版块热门帖子列表
    public function subNodeHot(SubNode $subNode)
    {
        $threads = $this->nodeBll->hotThreadsOfSubNode($subNode);

        return $threads;
    }

    //子版块最新帖子列表
    public function subNodeRecent(SubNode $subNode)
    {
        $threads = $this->nodeBll->recentThreadsOfSubNode($subNode);

        return $threads;
    }

    //精华帖子，按照加精时间和发表时间排序
    public function excellent(Node $node)
    {
        $threads = $this->nodeBll->excellentThreadsOfNode($node);

        return $threads;
    }
}