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
use Hifone\Models\Section;

class NodeController extends AppController
{
    public function index()
    {
        return Node::orderBy('order')->get();
    }

    public function sectionsWithNodes()
    {
        $sections = Section::orderBy('order')->get();
        foreach ($sections as $section) {
            $nodes = $section->nodes;
            foreach ($nodes as $node) {
                $subNodes = $node->subNodes()->orderBy('order')->get();
                $node['subNodes'] = $subNodes;
            }
            $sections['nodes'] = $nodes;
        }
        return $sections;
    }

    public function subNodes(Node $node)
    {
        return $node->subNodes()->orderBy('order', 'desc')->get();
    }
}