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
use Auth;

class NodeController extends AppController
{
    public function index()
    {
        return Node::orderBy('order')->get();
    }

    public function sections()
    {
        //除去无子版块的版块信息,同时判断用户身份决定是否显示公告活动等板块
        $sections = Section::orderBy('order')->with(['nodes.subNodes', 'nodes' => function ($query) {
            if (Auth::user()->can('manage_threads')) {
                $query->has('subNodes');
            } else {
                $query->show()->has('subNodes');
            }
        }])->get();

        return $sections;
    }

}