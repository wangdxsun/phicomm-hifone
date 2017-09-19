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
use Hifone\Models\Moderator;
use Hifone\Models\Node;
use Hifone\Models\Role;
use Hifone\Models\Section;
use Hifone\Models\SubNode;
use Hifone\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Mockery\Exception;

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
            'sub_title'     => '主板块管理',
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
        ->withPageTitle('主板块管理')
        ->withNodes($nodes);
    }

    public function show(Node $node)
    {
        $subNodes = SubNode::where('node_id',$node->id)->orderBy('order')->get();
        if (0 == count($subNodes))
            return Redirect::route('dashboard.node.index')
                ->withErrors('该主板块不存在子版块');
        return View::make('dashboard.subNodes.index')
            ->withPageTitle('子版块管理')
            ->with('subNodes',$subNodes);
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
            ->withPageTitle('添加主板块');
    }

    /**
     * Creates a new node.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $userData = Request::get('user');
        $nodeData = Request::get('node');
        $moderatorData = Request::get('moderator');
        if ('' != $userData['name']) {
            $user = User::findByUsernameOrFail($userData['name']);
            $user->role_id = $moderatorData['role'];
            $moderatorData['user_id'] = $user->id;
        }
        $nodeData['order'] = Node::max('order') + 1;

        try {
            $node = Node::create($nodeData);
            $moderatorData['node_id'] = $node->id;
            if ('' != $userData['name']) {
                $moderator = Moderator::create($moderatorData);
                $this->updateOpLog($moderator, '新增版主');
            }
            $this->updateOpLog($node, '新增主板块');
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
            ->withModerators(Moderator::get())
            ->withRole(Role::get())
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
        $moderatorData = Request::get('moderator');
        $userData = Request::get('user');
        if ('' != $userData['name']) {
            $user = User::findByUsernameOrFail($userData['name']);
            $user->role_id = $moderatorData['role'];
            $moderatorData['user_id'] = $user->id;
        }
        $moderatorData['node_id'] = $node->id;
        try {
            $node->update($nodeData);
            if ('' != $userData['name']) {
                $moderator = Moderator::create($moderatorData);
                $this->updateOpLog($moderator, '新增版主');
            }
            $this->updateOpLog($node, '修改板块');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.node.edit', ['id' => $node->id])
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.nodes.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.node.index')
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
        if ($node->subNodes()->count() > 0 || $node->threads()->count() > 0) {
            return back()->withErrors('该板块下存在帖子或子板块，无法删除');
        }
        $this->updateOpLog($node, '删除板块');
        $node->delete();

        return back()->withSuccess('板块删除成功');
    }

    public function auditToTrash(Moderator $moderator)
    {
        try {
            if (Moderator::where('user_id',$moderator->user_id)->count() == 1) {
                $moderator->user->role_id = 0;
                $moderator->delete();
            }
            $moderator->delete();
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessageBag());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

}
