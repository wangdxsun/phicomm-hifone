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
use Hifone\Models\PraModerator;
use Hifone\Models\Role;
use Hifone\Models\Section;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Redirect;
use Request;
use View;

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
            'sub_title'     => '主版块管理',
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
        ->withPageTitle('主版块管理')
        ->withNodes($nodes);
    }

    public function show(Node $node)
    {
        $subNodes = SubNode::where('node_id',$node->id)->orderBy('order')->get();
        if (0 == count($subNodes))
            return Redirect::route('dashboard.node.index')
                ->withErrors('该主版块不存在子版块');
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
        //添加主板块时，为页面传入所有组别为版主和实习版主的用户
        $moderators = Role::where('name', 'NodeMaster')->first()->users;
        $praModerators = Role::where('name', 'NodePraMaster')->first()->users;
        return View::make('dashboard.nodes.create_edit')
            ->withSections(Section::orderBy('order')->get())
            ->with('moderators', json_encode($moderators->toArray()))
            ->with('praModerators', json_encode($praModerators->toArray()))
            ->withPageTitle('添加主版块');
    }

    /**
     * Creates a new node.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->validate(request(),[
            'node.icon'                => 'required',
            'node.icon_list'           => 'required',
            'node.icon_detail'         => 'required',
            'node.android_icon'        => 'required',
            'node.android_icon_list'   => 'required',
            'node.android_icon_detail' => 'required',
            'node.ios_icon'            => 'required',
            'node.ios_icon_list'       => 'required',
            'node.ios_icon_detail'     => 'required',
            'node.web_icon_detail'     => 'required',
            'node.web_icon_list'       => 'required',
            'node.feedback_thread_id'  => 'numeric',
        ], [
            'node.icon.required'               => 'H5首页热门版块图片是必填字段',
            'node.icon_list.required'          => 'H5版块列表图片是必填字段',
            'node.icon_detail.required'        => 'H5版块详情页是必填字段',
            'node.ios_icon.required'           => 'IOS首页热门版块图片是必填字段',
            'node.ios_icon_list.required'      => 'IOS版块列表图片是必填字段',
            'node.ios_icon_detail.required'    => 'IOS版块详情页是必填字段',
            'node.android_icon.required'       => '安卓首页热门版块图片是必填字段',
            'node.android_icon_list.required'  => '安卓版块列表图片是必填字段',
            'node.android_icon_detail.required'=> '安卓版块详情页是必填字段',
            'node.web_icon_list.required'      => 'WEB右侧列表图片是必填字段',
            'node.web_icon_detail.required'    => 'WEB版块详情页是必填字段',
            'node.feedback_thread_id.numeric'  => '帖子id是数字类型',
        ]);
        $nodeData = Request::get('node');

        $moderatorData = explode(',', Request::get('nodeModerators'));
        $praModeratorData = explode(',', Request::get('nodePraModerators'));
        $nodeData['order'] = Node::max('order') + 1;
        try {
            $node = Node::create($nodeData);
            if (count($moderatorData) > 4 || count($praModeratorData) > 4 || count($moderatorData) + count($praModeratorData) > 4) {
                return Redirect::back()
                    ->withInput()
                    ->withErrors('最多支持添加四个版主！');
            }
            if (count($moderatorData) > 0) {
                foreach ($moderatorData as $userId) {
                   Moderator::create(['node_id' => $node->id, 'user_id' => $userId]);
                }
            }

            if (count($praModeratorData) > 0) {
                foreach ($praModeratorData as $userId) {
                    PraModerator::create(['node_id' => $node->id, 'user_id' => $userId]);
                }
            }
            $this->updateOpLog($node, '新增主版块');
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
        //添加主板块时，为页面传入所有组别为版主和实习版主的用户
        $moderators = Role::where('name', 'NodeMaster')->first()->users;
        $praModerators = Role::where('name', 'NodePraMaster')->first()->users;
        //主板块的版主和实习版主
        $nodeModerators = $node->moderators;
        $nodePraModerators = $node->praModerators;
        return View::make('dashboard.nodes.create_edit')
            ->withPageTitle(trans('dashboard.nodes.edit.title').' - '.trans('dashboard.dashboard'))
            ->withSections(Section::orderBy('order')->get())
            ->with('moderators', json_encode($moderators->toArray()))
            ->with('praModerators', json_encode($praModerators->toArray()))
            ->with('nodeModerators', json_encode($nodeModerators->pluck('id')->toArray()))
            ->with('nodePraModerators', json_encode($nodePraModerators->pluck('id')->toArray()))
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
        $this->validate(request(),[
            'node.icon'                => 'required',
            'node.icon_list'           => 'required',
            'node.icon_detail'         => 'required',
            'node.android_icon'        => 'required',
            'node.android_icon_list'   => 'required',
            'node.android_icon_detail' => 'required',
            'node.ios_icon'            => 'required',
            'node.ios_icon_list'       => 'required',
            'node.ios_icon_detail'     => 'required',
            'node.web_icon_detail'     => 'required',
            'node.web_icon_list'       => 'required',
            'node.feedback_thread_id'  => 'numeric',
        ], [
            'node.icon.required'               => 'H5首页热门版块图片是必填字段',
            'node.icon_list.required'          => 'H5版块列表图片是必填字段',
            'node.icon_detail.required'        => 'H5版块详情页是必填字段',
            'node.ios_icon.required'           => 'IOS首页热门版块图片是必填字段',
            'node.ios_icon_list.required'      => 'IOS版块列表图片是必填字段',
            'node.ios_icon_detail.required'    => 'IOS版块详情页是必填字段',
            'node.android_icon.required'       => '安卓首页热门版块图片是必填字段',
            'node.android_icon_list.required'  => '安卓版块列表图片是必填字段',
            'node.android_icon_detail.required'=> '安卓版块详情页是必填字段',
            'node.web_icon_list.required'      => 'WEB右侧列表图片是必填字段',
            'node.web_icon_detail.required'    => 'WEB版块详情页是必填字段',
            'node.feedback_thread_id.numeric'  => '帖子id是数字类型',
         ]);
        $nodeData = Request::get('node');
        $moderatorData = Request::get('nodeModerators')== "" ? [] : explode(',', Request::get('nodeModerators'));
        $praModeratorData = Request::get('nodePraModerators')== "" ? [] : explode(',', Request::get('nodePraModerators'));
        if (count($moderatorData) > 4 || count($praModeratorData) > 4 || count($moderatorData) + count($praModeratorData) > 4) {
            return Redirect::back()->withErrors('最多支持添加四个版主！');
        }
        try {
            $node->update($nodeData);
            $node->moderators()->sync($moderatorData);
            $node->praModerators()->sync($praModeratorData);
            $this->updateOpLog($node, '修改版块');
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
            return back()->withErrors('该版块下存在帖子或子版块，无法删除');
        }
        $this->updateOpLog($node, '删除版块');
        $node->delete();

        return back()->withSuccess('版块删除成功');
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
