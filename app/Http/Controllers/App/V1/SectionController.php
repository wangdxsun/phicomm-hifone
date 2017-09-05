<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/8/25
 * Time: 16:21
 */

namespace Hifone\Http\Controllers\App\V1;

use Hifone\Http\Controllers\App\AppController;
use Hifone\Models\Section;

class SectionController extends AppController
{
    public function index()
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
        dd($sections->toArray());
        return Section::with('nodes')->get();
    }
}