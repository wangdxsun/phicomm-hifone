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
use Hifone\Models\Thread;

class NodeController extends ApiController
{
    public function index()
    {
        return Node::orderBy('order')->get();
    }

    public function sections()
    {
        //除去无子版块的版块信息
        $sections = Section::orderBy('order')->with(['nodes.subNodes', 'nodes' => function ($query) {
            $query->has('subNodes');
        }])->get();
        return $sections;
    }

    public function show(Node $node, NodeBll $nodeBll)
    {
        $hot = $nodeBll->hotThreads($node);
        $recent = $nodeBll->recentThreads($node);
        $moderators = $node->moderators()->with(['user'])->get();

        $node['hot'] = $hot;
        $node['recent'] = $recent;
        $node['moderators'] = $moderators;

        return $node;
    }
}