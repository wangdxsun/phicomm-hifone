<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers\Dashboard;

use AltThree\Validator\ValidationException;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Node;
use Hifone\Models\Section;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

class NodeController extends Controller
{
    /**
     * Creates a new node controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share([
            'current_menu'  => 'nodes',
            'sub_title'     => '板块管理',
        ]);
    }

    /**
     * Shows the nodes view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $nodes = Node::orderBy('order')->get();

        return View::make('dashboard.nodes.index')
        ->withPageTitle('板块管理')
        ->withNodes($nodes);
    }

    /**
     * Shows the add node view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return View::make('dashboard.nodes.create_edit')
            ->withSections(Section::orderBy('order')->get())
            ->withPageTitle('添加板块');
    }

    /**
     * Creates a new node.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $nodeData = Request::get('node');

        try {
            $node = Node::create($nodeData);
            $this->updateOpLog($node, '新增板块');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.node.create')
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.nodes.add.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.node.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.nodes.add.success')));
    }

    /**
     * Shows the edit node view.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(Node $node)
    {
        return View::make('dashboard.nodes.create_edit')
            ->withPageTitle(trans('dashboard.nodes.edit.title').' - '.trans('dashboard.dashboard'))
            ->withSections(Section::orderBy('order')->get())
            ->withNode($node);
    }

    /**
     * Edit an node.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Node $node)
    {
        $nodeData = Request::get('node');

        try {
            $node->update($nodeData);
            $this->updateOpLog($node, '修改板块');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.node.edit', ['id' => $node->id])
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.nodes.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.node.edit', ['id' => $node->id])
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.nodes.edit.success')));
    }

    /**
     * Deletes a given node.
     *
     * @param \Hifone\Models\Node $node
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Node $node)
    {
        if ($node->threads()->count() > 0) {
            return back()->withErrors('该板块下存在帖子，无法删除');
        }
        $this->updateOpLog($node, '删除板块');
        $node->delete();

        return back()->withSuccess('板块删除成功');
    }
}
