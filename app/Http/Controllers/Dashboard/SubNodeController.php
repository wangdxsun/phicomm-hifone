<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2017/8/16
 * Time: 13:57
 */
namespace Hifone\Http\Controllers\Dashboard;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Node;
use Hifone\Models\SubNode;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use AltThree\Validator\ValidationException;

class SubNodeController extends Controller
{
    public function __construct()
    {
        View::share([
            'current_menu'  => 'subNodes',
            'sub_title'     => '子板块管理',
        ]);
    }

    public function index()
    {
        $subNodes = SubNode::orderBy('order')->get();

        return View::make('dashboard.subNodes.index')
            ->withPageTitle('子板块管理')
            ->with('subNodes',$subNodes);
    }

    public function create()
    {
        $nodes = Node::orderby('order')->get();
        return View::make('dashboard.subNodes.create_edit')
            ->withNodes($nodes)
            ->withPageTitle('添加子板块');
    }

    public function edit(SubNode $subNode)
    {
        return View::make('dashboard.subNodes.create_edit')
            ->withPageTitle(trans('dashboard.nodes.edit.sub_title').' - '.trans('dashboard.dashboard'))
            ->withNodes(Node::orderBy('order')->get())
            ->with('subNode',$subNode);
    }

    public function update(SubNode $subNode)
    {
        $threads = $subNode->threads;
        $subNodeData = Request::get('subNode');
        try {
            $subNode->update($subNodeData);
            foreach ($threads as $thread) {
                $thread->update(['node_id' => $subNodeData['node_id']]);
            }
            $this->updateOpLog($subNode, '修改板块');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.subNode.edit', ['id' => $subNode->id])
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.nodes.edit.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.subNode.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.nodes.edit.success')));
    }

    public function store()
    {
        $subNodeData = Request::get('subNode');
        $subNodeData['order'] = SubNode::max('order') + 1;
        try {
            $subNode = SubNode::create($subNodeData);
            $this->updateOpLog($subNode, '新增子板块');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.subNode.create')
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.nodes.add.failure')))
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.subNode.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.nodes.add.success')));
    }

    public function destroy(SubNode $subNode)
    {
        //dd($subNode->threads);
        if ($subNode->threads()->count()> 0) {
            return back()->withErrors('该子板块下存在帖子，无法删除');
        }
        $this->updateOpLog($subNode, '删除板块');
        $subNode->delete();

        return back()->withSuccess('板块删除成功');
    }
}