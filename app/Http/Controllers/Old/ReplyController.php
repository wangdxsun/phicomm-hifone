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

use Hifone\Commands\Reply\RemoveReplyCommand;
use Hifone\Http\Bll\ReplyBll;
use Hifone\Models\Reply;
use Hifone\Services\Filter\WordsFilter;
use Input;
use Redirect;
use Config;

class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(ReplyBll $replyBll, WordsFilter $wordsFilter)
    {
        try {
            $reply = $replyBll->createReply();
            $reply = $replyBll->auditReply($reply, $wordsFilter);
            if ($reply->status == Reply::VISIBLE) {
                return Redirect::back()->withSuccess('审核通过，发表成功！');
            } else {
                return Redirect::back()->withSuccess('回复发表成功，请耐心等待审核');
            }
        } catch (\Exception $e) {
            return Redirect::back()->withInput(Input::all())->withErrors($e->getMessageBag());
        }
    }

    public function destroy(Reply $reply)
    {
        $this->needAuthorOrAdminPermission($reply->user_id);

        dispatch(new RemoveReplyCommand($reply));

        return Redirect::route('thread.show', $reply->thread_id)
            ->withSuccess(sprintf('%s %s', trans('hifone.awesome'), trans('hifone.success')));
    }
}