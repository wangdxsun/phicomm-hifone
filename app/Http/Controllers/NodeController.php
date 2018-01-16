<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers;

use Hifone\Http\Bll\NodeBll;
use Hifone\Models\Node;
use Hifone\Models\Section;
use Hifone\Models\Thread;
use Hifone\Repositories\Criteria\Thread\BelongsToNode;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use Config;
use Input;

class NodeController extends Controller
{
    public function index()
    {
        $sections = Section::orderBy('order')->get();

        return $this->view('nodes.index')
            ->withSections($sections);
    }

    public function show(Node $node, NodeBll $nodeBll)
    {
        $this->breadcrumb->push($node->name, $node->url);

        $threads = $nodeBll->threads($node, Input::get('filter'));
        $nodeBll->webUpdateActiveTime();

        return $this->view('threads.index')
            ->withThreads($threads)
            ->withNode($node);
    }

    public function showBySlug($slug, NodeBll $nodeBll)
    {
        $node = Node::where('slug', $slug)->firstOrFail();
        return $this->show($node, $nodeBll);
    }
}
