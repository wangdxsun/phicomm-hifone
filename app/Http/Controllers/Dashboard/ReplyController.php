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

use Hifone\Commands\Reply\RemoveReplyCommand;
use Hifone\Commands\Reply\UpdateReplyCommand;
use Hifone\Http\Controllers\Controller;
use Hifone\Models\Reply;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Parsers\Markdown;
use Illuminate\Support\Facades\View;
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
            'sub_title'    => trans_choice('dashboard.replies.replies', 2),
            'sub_header'   => '回帖管理',
        ]);
    }

    public function index()
    {
        $search = array_filter(Input::get('reply', []), function($value) {
            return !empty($value);
        });
        $replies = Reply::visible()->search($search)->orderBy('created_at', 'desc')->paginate(20);
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
            $reply = dispatch(new UpdateReplyCommand($reply, $replyData));
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
        dispatch(new RemoveReplyCommand($reply));

        return Redirect::route('dashboard.reply.index')
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function pin(Reply $reply)
    {
        ($reply->order > 0) ? $reply->decrement('order', 1) : $reply->increment('order', 1);

        return Redirect::route('dashboard.reply.index')
            ->withSuccess(trans('dashboard.reply.edit.success'));
    }

    public function audit()
    {
        $replies = Reply::audit()->orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.replies.audit')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)->withCurrentMenu('audit');
    }

    public function trash()
    {
        $search = array_filter(Input::get('reply', []), function($value) {
            return !empty($value);
        });
        $replies = Reply::trash()->search($search)->orderBy('created_at', 'desc')->paginate(20);
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
        $reply->order = 0;
        $reply->save();
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }

    public function postTrash(Reply $reply)
    {
        $reply->order = -1;
        $reply->save();
        return Redirect::back()->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}
