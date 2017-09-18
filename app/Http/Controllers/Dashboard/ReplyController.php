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
use Carbon\Carbon;
use Hifone\Commands\Reply\UpdateReplyCommand;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Reply\ReplyWasAddedEvent;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Services\Parsers\Markdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        return View::make('dashboard.replies.index')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)
            ->with('orderTypes',$orderTypes)
            ->withSearch($search)
            ->with('orderByThreadId',$orderByThreadId)
            ->withCurrentMenu('index');
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
        $menu = $reply->status == 0 ? 'index' : 'audit';
        return View::make('dashboard.replies.create_edit')
            ->withPageTitle(trans('dashboard.replies.edit.title').' - '.trans('dashboard.dashboard'))
            ->withReply($reply)->withCurrentMenu($menu);
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
        $replyData['body'] = (new Markdown())->convertMarkdownToHtml($replyData['body']);

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
            $this->updateOpLog($reply, '置顶');
        }else{
            $reply->increment('order', 1);
            $this->updateOpLog($reply, '取消置顶');
            event(new PinWasAddedEvent($reply->user, 'Reply'));
        }

        return Redirect::back()->withSuccess('恭喜，操作成功');
    }

    public function audit()
    {
        $replies = Reply::audit()->with('thread', 'user')->orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.replies.audit')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)->withCurrentMenu('audit');
    }

    public function trashView()
    {
        $search = $this->filterEmptyValue(Input::get('reply'));
        $replies = Reply::trash()->search($search)->with('thread', 'user', 'lastOpUser')->orderBy('last_op_time', 'desc')->paginate(20);
        $replyAll = Reply::trash()->get()->toArray();
        $threadIds = array_unique(array_column($replyAll, 'thread_id'));
        $userIds = array_unique(array_column($replyAll, 'user_id'));
        $operators = array_unique(array_column($replyAll, 'last_op_user_id'));
        $orderTypes = Reply::$orderTypes;


        return View::make('dashboard.replies.trash')
            ->withPageTitle('回复回收站')
            ->with('orderTypes',$orderTypes)
            ->withSearch($search)
            ->withReplies($replies)->withCurrentMenu('trash')
            ->withThreads(Thread::find($threadIds))
            ->withUsers(User::find($userIds))
            ->withOperators(User::find($operators));
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
        event(new RepliedWasAddedEvent($reply->user, $thread->user));

        return $this->passAudit($reply);
    }

    //从回收站恢复回复
    public function recycle($reply)
    {
        return $this->passAudit($reply);
    }

    //将回复状态修改为审核通过，回复所属帖子修改时间将被更新
    public function passAudit($reply)
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
            $reply->status = 0;
            $this->updateOpLog($reply, '审核通过');

            //把当前回复的创建时间和回复所属的帖子的修改时间进行比对
            //如果回复创建时间更新，则替换到帖子修改时间。否则，什么也不做。
            if ($reply->created_at > $reply->thread->updated_at) {
                $reply->thread->updated_at = $reply->created_at;
                $reply->thread->save();
            }

            event(new ReplyWasAuditedEvent($reply));
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
            $reply->thread->node->decrement('reply_count', 1);//版块回帖数-1
            $reply->thread->subNode->decrement('reply_count', 1);//子版块回帖数-1
            $reply->thread->decrement('reply_count', 1);
            $reply->user->decrement('reply_count', 1);
            $this->trash($reply);
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

    //将回复放到回收站
    public function trash(Reply $reply)
    {
        $reply->status = Reply::TRASH;
        $this->updateOpLog($reply, '删除回复', trim(request('reason')));
    }
}
