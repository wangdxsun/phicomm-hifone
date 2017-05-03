<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Http\Controllers;

use AltThree\Validator\ValidationException;
use Auth;
use Hifone\Commands\Reply\AddReplyCommand;
use Hifone\Commands\Reply\RemoveReplyCommand;
use Hifone\Http\Bll\ReplyBll;
use Hifone\Models\Reply;
use Input;
use Redirect;

class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Creates a new node.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ReplyBll $replyBll)
    {
        if (Auth::user()->hasRole('NoComment')) {
            return Redirect::back()->withErrors('您已被系统管理员禁言');
        }
        try {
            $replyBll->createReply();
        } catch (ValidationException $e) {
            return Redirect::back()
                ->withInput(Input::all())
                ->withErrors($e->getMessageBag());
        }

        return Redirect::back()
            ->withSuccess('回复发表成功，请耐心等待审核通过');
    }

    public function destroy(Reply $reply)
    {
        $this->needAuthorOrAdminPermission($reply->user_id);

        dispatch(new RemoveReplyCommand($reply));

        return Redirect::route('thread.show', $reply->thread_id)
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}
