<?php

namespace Hifone\Http\Controllers\Web;

use Hifone\Models\Node;

class SubNodeController extends WebController
{
    public function index(Node $node)
    {
        $subNodes = $node->subNodes()->orderBy('order')->get();
        $node['subNodes'] = $subNodes;
        return $node;
    }
}