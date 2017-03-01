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
        $replies = Reply::orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.replies.index')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)->withCurrentMenu('index');
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

    public function audit()
    {
        $replies = Reply::orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.replies.index')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)->withCurrentMenu('audit');
    }

    public function trash()
    {
        $replies = Reply::orderBy('created_at', 'desc')->paginate(20);

        return View::make('dashboard.replies.index')
            ->withPageTitle(trans('dashboard.replies.replies').' - '.trans('dashboard.dashboard'))
            ->withReplies($replies)->withCurrentMenu('trash');
    }
}