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

class NodeController extends AppController
{
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
     * @param NodeBll $nodeBll
     * @return mixed
     */
    public function sections(NodeBll $nodeBll)
    {
        $sections = $nodeBll->sections();
        foreach ($sections as $section) {
            foreach ($nodes = $section['nodes'] as $node) {
                $node['detail_url'] = route('app.node.show', $node->id);
            }
        }
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
     * 意见反馈发帖选择子版块
     * @param NodeBll $nodeBll
     */
    public function subNodesInFeedback(NodeBll $nodeBll)
    {
        $nodes = $nodeBll->subNodesInFeedback();
        return $nodes;
    }

}