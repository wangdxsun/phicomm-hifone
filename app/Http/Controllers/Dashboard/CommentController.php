<?php
namespace Hifone\Http\Controllers\Dashboard;

use Hifone\Events\Pin\PinWasAddedEvent;
use DB;
use View;
use Input;
use Redirect;
use Hifone\Models\Comment;
use Hifone\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('answer'));
        $comments = Comment::visible()->search($search)->with(['answer', 'answer.question'])->orderBy('last_op_time', 'desc')->paginate(20);
        $commentsCount = Comment::visible()->count();
        return View::make('dashboard.comments.index')
            ->with('comments', $comments)
            ->with('commentsCount', $commentsCount)
            ->with('search', $search)
            ->with('current_menu', 'comment')
            ->with('current_nav', 'index');
    }

    public function audit()
    {
        $comments = Comment::audit()->orderBy('last_op_time', 'desc')->paginate(20);
        $commentsCount = Comment::audit()->count();
        return View::make('dashboard.comments.audit')
            ->with('comments', $comments)
            ->with('commentsCount', $commentsCount)
            ->with('current_menu', 'comment')
            ->with('current_nav', 'audit');

    }

    public function trashView()
    {
        $search = $this->filterEmptyValue(Input::get('comment'));
        $comments = Comment::trash()->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $commentsCount = Comment::trash()->count();
        return View::make('dashboard.comments.trash')
            ->with('comments', $comments)
            ->with('commentsCount', $commentsCount)
            ->with('search', $search)
            ->with('current_menu', 'comment')
            ->with('current_nav', 'trash');
    }

    public function pin(Comment $comment)
    {
        //1.取消置顶
        if (1 == $comment->order) {
            $comment->update(['order' => 0]);
            $this->updateOpLog($comment, '取消置顶回复');
        } else {
            $comment->update(['order' => 1]);
            $this->updateOpLog($comment, '置顶回复');
            event(new PinWasAddedEvent($comment->user, $comment));
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //批量审核通过回复
    public function postBatchAudit() {
        $count = 0;
        $commentIds = Input::get('batch');
        if ($commentIds != null) {
            DB::beginTransaction();
            try {
                foreach ($commentIds as $id) {
                    self::postAudit(Comment::find($id));
                    $count++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return Redirect::back()->withErrors($e->getMessage());
            }
            return Redirect::back()->withSuccess('恭喜，批量操作成功！'.'共'.$count.'条');
        } else {
            return Redirect::back()->withErrors('您未选中任何记录！');
        }
    }

    //从待审核列表审核通过回复
    public function postAudit(Comment $comment)
    {
        return $this->passAudit($comment);
    }

    //将问题状态修改为审核通过,需要将问题数加1
    public function passAudit(Comment $comment)
    {
        DB::beginTransaction();
        try {
            $comment->status = Comment::VISIBLE;
            $comment->save();
            $this->updateOpLog($comment, '审核通过回复');
            $comment->user->update(['comment_count' => $comment->user->comments()->visibleAndDeleted()->count()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        //

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //从审核通过删除回复，users表中需要将回复数-1
    public function indexToTrash(Comment $comment)
    {
        DB::beginTransaction();
        try {
            $this->delete($comment);
            $comment->user->update(['comment_count' => $comment->user->comments()->visibleAndDeleted()->count()]);
            //TODO  事件
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }


    //从待审核删除提问
    public function auditToTrash(Comment $comment)
    {
        try {
            $this->trash($comment);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //审核通过列表的提问，放到回收站
    public function delete(Comment $comment)
    {
        $comment->status = Comment::DELETED;
        $this->updateOpLog($comment, '删除回复', trim(request('reason')));
    }

    //审核未通过列表的提问，放到回收站
    public function trash(Comment $comment)
    {
        $comment->status = Comment::TRASH;
        $this->updateOpLog($comment, '回复审核未通过', trim(request('reason')));
    }



}