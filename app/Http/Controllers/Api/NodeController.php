<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/4/24
 * Time: 19:33
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Http\Bll\NodeBll;
use Hifone\Models\Node;
use Hifone\Models\Section;
use Auth;
use Hifone\Models\SubNode;

class NodeController extends ApiController
{
    private $nodeBll;

    public function __construct(NodeBll $nodeBll)
    {
        $this->nodeBll = $nodeBll;
        parent::__construct();
    }

    public function index()
    {
        return Node::orderBy('order')->get();
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
    public function showOfSubNode(SubNode $subNode)
    {
        $node = $this->nodeBll->showOfSubNode($subNode);
        return $node;
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