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

    public function sections()
    {
        $sections = Section::orderBy('order')->with(['nodes.subNodes'])->get();
        return $sections;
    }

}