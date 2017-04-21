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
use Hifone\Commands\Reply\RemoveReplyCommand;
use Hifone\Commands\Reply\UpdateReplyCommand;
use Hifone\Events\Pin\PinWasAddedEvent;
use Hifone\Events\Reply\RepliedWasAddedEvent;
use Hifone\Events\Reply\ReplyWasAddedEvent;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Services\Parsers\Markdown;
use View;
use Input;
use Redirect;

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
        $replies = Reply::visible()->search($search)->with('thread', 'user', 'lastOpUser', 'thread.node')->orderBy('last_op_time', 'desc')->paginate(20);
        $replyAll = Reply::visible()->get()->toArray();
        $threadIds = array_unique(array_column($replyAll, 'thread_id'));
        $userIds = array_unique(array_column($replyAll, 'user_id'));

        return View::make('dashboard.replies.index')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)
            ->withCurrentMenu('index')
            ->withThreads(Thread::find($threadIds))
            ->withUsers(User::find($userIds));
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
        return View::make('dashboard.replies.create_edit')
            ->withPageTitle(trans('dashboard.replies.edit.title').' - '.trans('dashboard.dashboard'))
            ->withReply($reply)->withCurrentMenu('index');
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

    public function trash()
    {
        $search = $this->filterEmptyValue(Input::get('reply'));
        $replies = Reply::trash()->search($search)->with('thread', 'user', 'lastOpUser')->orderBy('last_op_time', 'desc')->paginate(20);
        $replyAll = Reply::trash()->get()->toArray();
        $threadIds = array_unique(array_column($replyAll, 'thread_id'));
        $userIds = array_unique(array_column($replyAll, 'user_id'));
        $operators = array_unique(array_column($replyAll, 'last_op_user_id'));

        return View::make('dashboard.replies.trash')
            ->withPageTitle('回复回收站')
            ->withReplies($replies)->withCurrentMenu('trash')
            ->withThreads(Thread::find($threadIds))
            ->withUsers(User::find($userIds))
            ->withOperators(User::find($operators));
    }

    public function postAudit(Reply $reply)
    {
        event(new ReplyWasAddedEvent($reply));
        event(new RepliedWasAddedEvent($reply->thread->user));

        return $this->passAudit($reply);
    }

    public function recycle($reply)
    {
        return $this->passAudit($reply);
    }

    public function passAudit($reply)
    {
        try {
            $reply->status = 0;
            $this->updateOpLog($reply, '审核通过');
        } catch (ValidationException $e) {
            return Redirect::back()->withErrors($e->getMessageBag());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    public function postTrash(Reply $reply)
    {
        try {
            $reply->status = -1;
            $this->updateOpLog($reply, trim(request('reason')));
        } catch (ValidationException $e) {
            return Redirect::back()->withErrors($e->getMessageBag());
        }
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}
