<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 15:37
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Node;

class NodeController extends AppController
{
    public function index()
    {
        return Node::orderBy('order')->get();
    }

    public function show(Node $node)
    {

    }
}