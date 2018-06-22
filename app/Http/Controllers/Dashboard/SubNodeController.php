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
use Redirect;
use Request;
use View;

class SubNodeController extends Controller
{
    public function __construct()
    {
        View::share([
            'current_menu'  => 'subNodes',
            'sub_title'     => '子版块管理',
        ]);
    }

    public function index()
    {
        $subNodes = SubNode::orderBy('order')->with(['node'])->get();

        return View::make('dashboard.subNodes.index')
            ->withPageTitle('子版块管理')
            ->with('subNodes',$subNodes);
    }

    public function create()
    {
        $nodes = Node::orderby('order')->get();
        return View::make('dashboard.subNodes.create_edit')
            ->withNodes($nodes)
            ->withPageTitle('添加子版块');
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
        $this->validate(request(),[
            'subNode.name'                => 'required|max:6'
        ], [
            'subNode.name.max'            => '板块名称需1-6个字符',
            'subNode.name.required'       => '板块名称需1-6个字符',
        ]);
        $subNodeData = Request::get('subNode');
        try {
            if ($subNode->node_id <> $subNodeData['node_id']) {
                $oldNode = Node::find($subNode->node_id);
                $newNode = Node::find($subNodeData['node_id']);
                $subNode->threads()->update(['node_id' => $newNode->id]);
                $oldNode->update(['thread_count' => $oldNode->threads()->visible()->count()]);
                $oldNode->update(['reply_count' => $oldNode->replies()->where('replies.status', 0)->count()]);
                $newNode->update(['thread_count' => $newNode->threads()->visible()->count()]);
                $newNode->update(['reply_count' => $newNode->replies()->where('replies.status', 0)->count()]);
            }
            $subNode->update($subNodeData);

            $this->updateOpLog($subNode, '修改子版块');
        } catch (\Exception $e) {
            return Redirect::route('dashboard.subNode.edit', ['id' => $subNode->id])
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.nodes.edit.failure')))
                ->withErrors($e->getMessage());
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
            $this->updateOpLog($subNode, '新增子版块');
        } catch (\Exception $e) {
            return Redirect::route('dashboard.subNode.create')
                ->withInput(Request::all())
                ->withTitle(sprintf('%s %s', trans('hifone.whoops'), trans('dashboard.nodes.add.failure')))
                ->withErrors($e->getMessage());
        }

        return Redirect::route('dashboard.subNode.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('dashboard.nodes.add.success')));
    }

    public function destroy(SubNode $subNode)
    {
        if ($subNode->threads()->count()> 0) {
            return back()->withErrors('该子版块下存在帖子，无法删除');
        }
        $this->updateOpLog($subNode, '删除版块');
        $subNode->delete();

        return back()->withSuccess('版块删除成功');
    }
}