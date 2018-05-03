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
use Hifone\Commands\Reply\UpdateReplyCommand;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Reply\ReplyWasAddedEvent;
use Hifone\Events\Reply\ReplyWasPinnedEvent;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Illuminate\Support\Facades\DB;
use View;
use Input;
use Redirect;
use Hifone\Events\Reply\ReplyWasAuditedEvent;
use Hifone\Events\Reply\ReplyWasTrashedEvent;

class ReplyController extends Controller
{
    /**
     * Creates a new reply controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        View::share([
            'sub_header'   => '回帖管理',
        ]);
    }

    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('reply'));
        $replies = Reply::visible()->search($search)->with('thread', 'user', 'lastOpUser', 'thread.node')
            ->orderBy('last_op_time', 'desc')->paginate(20);
        $orderTypes = Reply::$orderTypes;
        $orderByThreadId = Reply::$orderByThreadId;
        $replyCount = Reply::visible()->count();
        return View::make('dashboard.replies.index')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)
            ->with('replyCount', $replyCount)
            ->with('orderTypes',$orderTypes)
            ->withSearch($search)
            ->with('orderByThreadId',$orderByThreadId)
            ->withCurrentNav('index');
    }

    public function audit()
    {
        $replies = Reply::audit()->with('thread', 'user')->orderBy('created_at', 'desc')->paginate(20);
        $replyCount = Reply::audit()->count();

        return View::make('dashboard.replies.audit')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)
            ->with('replyCount', $replyCount)
            ->withCurrentNav('audit');
    }

    public function trashView()
    {
        $search = $this->filterEmptyValue(Input::get('reply'));
        $replies = Reply::trash()->search($search)->with('thread', 'user', 'lastOpUser')->orderBy('last_op_time', 'desc')->paginate(20);
        $replyCount = Reply::trash()->count();
        $replyAll = Reply::trash()->get()->toArray();
        $threadIds = array_unique(array_column($replyAll, 'thread_id'));
        $userIds = array_unique(array_column($replyAll, 'user_id'));
        $operators = array_unique(array_column($replyAll, 'last_op_user_id'));
        $orderTypes = Reply::$orderTypes;


        return View::make('dashboard.replies.trash')
            ->withPageTitle('回复回收站')
            ->with('orderTypes',$orderTypes)
            ->withSearch($search)
            ->with('replyCount', $replyCount)
            ->withReplies($replies)
            ->withCurrentNav('trash')
            ->withThreads(Thread::find($threadIds))
            ->withUsers(User::find($userIds))
            ->withOperators(User::find($operators));
    }

    /**
     * Shows the edit reply view.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */
    public function edit(Reply $reply)
    {
        $menu = $reply->status == Reply::VISIBLE ? 'index' : 'audit';
        return View::make('dashboard.replies.create_edit')
            ->withPageTitle(trans('dashboard.replies.edit.title').' - '.trans('dashboard.dashboard'))
            ->withReply($reply)
            ->withCurrentNav($menu);
    }

    /**
     * Edit a reply.
     *
     * @param \Hifone\Models\Reply $reply
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Reply $reply)
    {
        $replyData = Input::get('reply');

        $replyData['body_original'] = $replyData['body'];

        try {
            dispatch(new UpdateReplyCommand($reply, $replyData));
            $this->updateOpLog($reply, '修改回复');
        } catch (ValidationException $e) {
            return Redirect::route('dashboard.reply.edit', $reply->id)
                ->withInput($replyData)
                ->withErrors($e->getMessageBag());
        }

        return Redirect::route('dashboard.reply.edit', ['id' => $reply->id])
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    /**
     * Destroy a reply.
     *
     * @param \Hifone\Models\Reply $reply
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Reply $reply)
    {
        $this->updateOpLog($reply, '删除回复');
        $reply->delete();

        return Redirect::back()->withSuccess('回帖删除成功');
    }

    public function pin(Reply $reply)
    {
        if($reply->order > 0){
            $reply->decrement('order', 1);
            $this->updateOpLog($reply, '取消置顶');
        }else{
            $reply->increment('order', 1);
            $this->updateOpLog($reply, '置顶');
            event(new PinWasAddedEvent($reply->user,  $reply));
            event(new ReplyWasPinnedEvent($reply));
        }

        return Redirect::back()->withSuccess('恭喜，操作成功');
    }

    //批量审核通过回帖
    public function postBatchAudit() {
        $count = 0;
        $reply_ids = Input::get('batch');
        if ($reply_ids != null) {
            DB::beginTransaction();
            try {
                foreach ($reply_ids as $id) {
                    if (Reply::find($id)){
                        self::postAudit(Reply::find($id));
                        $count++;
                    }
                }
                DB::commit();
            } catch (ValidationException $e) {
                DB::rollBack();
                return Redirect::back()->withErrors($e->getMessageBag());
            }
            return Redirect::back()->withSuccess('恭喜，批量操作成功！'.'共'.$count.'条');
        } else {
            return Redirect::back()->withErrors('您未选中任何记录！');
        }
    }

    //从待审核列表审核通过回复
    public function postAudit(Reply $reply)
    {
        $thread = $reply->thread;
        $thread->last_reply_user_id = $reply->user_id;
        //审核通过时不再更新帖子的修改时间，取最近一次审核通过回复的创建时间
        $thread->save();
        event(new ReplyWasAddedEvent($reply));
        event(new RepliedWasAddedEvent($reply->user, $thread->user, $reply));

        return $this->passAudit($reply);
    }

    //从回收站恢复回复
    public function recycle(Reply $reply)
    {
        return $this->passAudit($reply);
    }

    //将回复状态修改为审核通过，回复所属帖子修改时间将被更新
    public function passAudit(Reply $reply)
    {
        DB::beginTransaction();
        try {
            $reply->thread->node->increment('reply_count', 1);//版块回帖数+1
            if ($reply->thread->subNode) {
                $reply->thread->subNode->increment('reply_count', 1);//子版块回帖数+1
            }

            $reply->thread->increment('reply_count', 1);
            if ($reply->user) {
                $reply->user->increment('reply_count', 1);
            }
            $reply->status = Reply::VISIBLE;
            $this->updateOpLog($reply, '审核通过');

            //把当前回复的创建时间和回复所属的帖子的修改时间进行比对
            //如果回复创建时间更新，则替换到帖子修改时间。否则，什么也不做。
            if ($reply->created_at > $reply->thread->updated_at) {
                $reply->thread->updated_at = $reply->created_at;
                $reply->thread->save();
            }

            event(new ReplyWasAuditedEvent($reply));
            $reply->thread->updateIndex();
            DB::commit();
        } catch (ValidationException $e) {
            DB::rollback();
            return Redirect::back()->withErrors($e->getMessageBag());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //从审核通过删除回复，需要将回帖数-1
    public function indexToTrash(Reply $reply)
    {
        DB::beginTransaction();
        try {
            $this->delete($reply);
            $reply->thread->node->decrement('reply_count', 1);//版块回帖数-1
            $reply->thread->subNode->decrement('reply_count', 1);//子版块回帖数-1
            $reply->thread->update(['reply_count' => $reply->thread->replies()->visibleAndDeleted()->count()]);
            $reply->user->update(['reply_count' => $reply->user->replies()->visibleAndDeleted()->count()]);
            $reply->thread->updateIndex();
            event(new ReplyWasTrashedEvent($reply));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return Redirect::back()->withErrors($e->getMessageBag());
        }
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    //从待审核删除回复
    public function auditToTrash(Reply $reply)
    {
        try {
            $this->trash($reply);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessageBag());
        }
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    //删除正常回复，放到回收站
    public function delete(Reply $reply)
    {
        $reply->status = Reply::DELETED;
        $this->updateOpLog($reply, '删除回复', trim(request('reason')));
    }

    //回复审核未通过，放到回收站
    public function trash(Reply $reply)
    {
        $reply->status = Reply::TRASH;
        $this->updateOpLog($reply, '回复审核未通过', trim(request('reason')));
    }
}
