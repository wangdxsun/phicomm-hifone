<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/4/24
 * Time: 19:33
 */

namespace Hifone\Http\Controllers\Api;

use Hifone\Models\Node;

class NodeController extends AbstractApiController
{
    public function index()
    {
        return Node::orderBy('order')->get();
    }
}