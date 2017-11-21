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
use DB;
use Hifone\Http\Bll\ChatBll;

class ChatController extends Controller
{
    public function sendChat()
    {
        return View::make('dashboard.chat.index')
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
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        $data = Request::get('chat');
        if (empty($data['userType'])) {
            return Redirect::route('dashboard.chat.send')
                ->withErrors('没有选择用户类型')
                ->withInput();
        } elseif (empty(Request::get('message')) && empty(Request::get('imageUrl'))) {
            return Redirect::route('dashboard.chat.send')
                ->withErrors('文字、图片不能同时为空')
                ->withInput();
        } elseif ($data['userType'] == 3) {
            //为所有用户发送私信
            User::where('id', '<>', Auth::user()->id)->chunk(1000, function($users) use ($chatBll){
                $chatBll->batchNewMessage($users);
            });
            User::where('id', '<>', Auth::user()->id)->increment('notification_chat_count',1);
            User::where('id', '<>', Auth::user()->id)->increment('notification_count',1);
            return Redirect::route('dashboard.chat.send')
                ->withSuccess('成功为所有用户发送私信')
                ->withInput();
        } elseif ($data['userType'] == 6) {
            //为特定帖子内满足条件的用户发送私信
            $thread = Thread::find($data['thread_id']);
            if (null == $thread) {
                return Redirect::route('dashboard.chat.send')
                    ->withErrors('请输入有效的帖子ID')
                    ->withInput();
            }
            $replies = $thread->replies()->visible()->search($data)->where('user_id', '<>', Auth::user()->id)->get()->unique('user_id');
            $userIds = [];
            foreach ($replies as $reply) {
                $userIds[] = $reply->user->id;
            }
            $users = User::whereIn('id',$userIds)->whereNotIn('id', [Auth::user()->id])->get();
            $chatBll->batchNewMessage($users);
            return Redirect::route('dashboard.chat.send')
                ->withSuccess('成功为帖子'.$data['thread_id']. '内满足条件的所有用户发送私信')
                ->withInput();
        } elseif ($data['userType'] == 9) {
            $userIds = explode(',',$data['userIds']);
            $users = User::whereIn('id', $userIds)->whereNotIn('id', [Auth::user()->id])->get();
            $chatBll->batchNewMessage($users);
            return Redirect::route('dashboard.chat.send')
                ->withSuccess('成功为满足条件的所有用户发送私信');
        } else {
            return Redirect::route('dashboard.chat.send')
                ->withErrors('用户类型选择错误')
                ->withInput();
        }
    }
}