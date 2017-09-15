<?php
namespace Hifone\Http\Controllers\Api;

use Hifone\Models\Node;

class SubNodeController extends ApiController
{
    public function index(Node $node)
    {
        $subNodes = $node->subNodes()->orderBy('order')->get();
        $node['subNodes'] = $subNodes;
        return $node;
    }
}