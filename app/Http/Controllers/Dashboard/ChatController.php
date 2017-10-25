<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Http\Controllers\Controller;
use Hifone\Models\Chat;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Redirect;
use View;
use Request;
use Auth;
use Input;
use Hifone\Http\Bll\ChatBll;

class ChatController extends Controller
{
    public function sendChat()
    {
        return View::make('dashboard.chat.index')
            ->withUsers(User::all())
            ->withCurrentMenu('send');

    }

    public function chatLists()
    {
        $search = $this->filterEmptyValue(Input::get('chat'));
        $chats = Chat::search($search)->orderBy('created_at','desc')->paginate(20);
        return View::make('dashboard.chat.lists')
            ->withChats($chats)
            ->withSearch($search)
            ->withCurrentMenu('lists');
    }

    public function chatStore(ChatBll $chatBll)
    {
        $data = Request::get('chat');
        if (empty($data['userType'])) {
            return Redirect::route('dashboard.chat.send')
                ->withErrors('没有选择用户类型');
        } elseif (empty(Request::get('message')) && empty(Request::get('imageUrl'))) {
            return Redirect::route('dashboard.chat.send')
                ->withErrors('文字、图片不能同时为空');
        } elseif ($data['userType'] == 3) {
            //为所有用户发送私信
            $users = User::all();
            foreach ($users as $user) {
                if ($user->id == Auth::user()->id) {
                    continue;
                }
                $chatBll->newMessage($user);
            }
            return Redirect::route('dashboard.chat.send')
                ->withSuccess('成功为所有用户发送私信');
        } elseif ($data['userType'] == 6) {
            //为特定帖子内满足条件的用户发送私信
            $thread = Thread::find($data['thread_id']);
            if (null == $thread) {
                return Redirect::route('dashboard.chat.send')
                    ->withErrors('请输入有效的帖子ID');
            }
            $replies = $thread->replies()->visible()->search($data)->get()->unique('user_id');
            foreach ($replies as $reply) {
                if ($reply->user->id == Auth::user()->id) {
                    continue;
                }
                $chatBll->newMessage($reply->user);
            }
            return Redirect::route('dashboard.chat.send')
                ->withSuccess('成功为帖子'.$data['thread_id']. '内满足条件的所有用户发送私信');
        } elseif ($data['userType'] == 9) {
            foreach (explode(',',$data['userIds']) as $user_id) {
                if ($user_id == Auth::user()->id) {
                    continue;
                }
                $chatBll->newMessage(User::find($user_id));
            }
            return Redirect::route('dashboard.chat.send')
                ->withSuccess('成功为满足条件的所有用户发送私信');
        } else {
            return Redirect::route('dashboard.chat.send')
                ->withErrors('用户类型选择错误');
        }
    }
}