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
        try{
            $reply = $replyBll->createReply();
            $badWord = '';
            if (Config::get('setting.auto_audit', 0) == 0 || ($badWord = $wordsFilter->filterWord($reply->body)) || $replyBll->isContainsImageOrUrl($reply->body)) {
                $reply->bad_word = $badWord;
                $reply->body = app('parser.at')->parse($reply->body);
                $reply->body = app('parser.emotion')->parse($reply->body);
                $reply->save();
                return Redirect::back()->withSuccess('回复发表成功，请耐心等待审核');
            }
            $reply->body = app('parser.at')->parse($reply->body);
            $reply->body = app('parser.emotion')->parse($reply->body);
            $reply->save();
            $replyBll->replyPassAutoAudit($reply);
            return Redirect::back()->withSuccess('审核通过，发表成功！');

        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput(Input::all())
                ->withErrors($e->getMessageBag());
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
