<?php
namespace Hifone\Http\Controllers\Dashboard;

use Carbon\Carbon;
use Hifone\Commands\Comment\UpdateCommentCommand;
use DB;
use Hifone\Events\Comment\CommentedWasAddedEvent;
use Hifone\Events\Comment\CommentWasAuditedEvent;
use Hifone\Models\TagType;
use View;
use Input;
use Redirect;
use Hifone\Models\Comment;
use Hifone\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index()
    {
        $search = $this->filterEmptyValue(Input::get('comment'));
        $comments = Comment::visible()->search($search)->with(['user', 'answer', 'answer.question.tags'])->orderBy('last_op_time', 'desc')->paginate(20);
        $commentsCount = Comment::visible()->count();
        //问题分类及相应问题子类
        $questionTagTypes = TagType::ofType([TagType::QUESTION])->with('tags')->get();
        return View::make('dashboard.comments.index')
            ->with('comments', $comments)
            ->with('commentsCount', $commentsCount)
            ->with('search', $search)
            ->with('questionTagTypes', $questionTagTypes)
            ->with('current_menu', 'comment')
            ->with('current_nav', 'index');
    }

    public function audit()
    {
        $comments = Comment::audit()->with(['user', 'answer', 'answer.question.tags'])->orderBy('last_op_time', 'desc')->paginate(20);
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
        $comments = Comment::trash()->with(['user', 'answer', 'answer.question.tags'])->search($search)->orderBy('last_op_time', 'desc')->paginate(20);
        $commentsCount = Comment::trash()->count();
        return View::make('dashboard.comments.trash')
            ->with('comments', $comments)
            ->with('commentsCount', $commentsCount)
            ->with('search', $search)
            ->with('current_menu', 'comment')
            ->with('current_nav', 'trash');
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

    //从回收站审核通过回复
    public function recycle(Comment $comment)
    {
        return $this->passAudit($comment);
    }

    //将回复状态修改为审核通过,需要将回复数加1
    public function passAudit(Comment $comment)
    {
        DB::beginTransaction();
        try {
            $comment->status = Comment::VISIBLE;
            $this->updateOpLog($comment, '审核通过回复');
            $comment->user->update(['comment_count' => $comment->user->comments()->visibleAndDeleted()->count()]);
            $comment->answer->update([
                'comment_count' => $comment->answer->comments()->visibleAndDeleted()->count(),
                'last_comment_time' => Carbon::now()->toDateTimeString()
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        //回复被审核通过
        event(new CommentWasAuditedEvent($comment->user, $comment));
        //回答被回复
        event(new CommentedWasAddedEvent($comment->user, $comment->answer));

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //从审核通过删除回复，users表中需要将回复数-1
    public function indexToTrash(Comment $comment)
    {
        DB::beginTransaction();
        try {
            $this->delete($comment);
            $comment->user->update(['comment_count' => $comment->user->comments()->visibleAndDeleted()->count()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withErrors($e->getMessage());
        }
        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }


    //从待审核删除回复
    public function auditToTrash(Comment $comment)
    {
        try {
            $this->trash($comment);
        } catch (\Exception $e) {
            return Redirect::back()->withErrors($e->getMessage());
        }

        return Redirect::back()->withSuccess('恭喜，操作成功！');
    }

    //审核通过列表的回复，放到回收站
    public function delete(Comment $comment)
    {
        $comment->status = Comment::DELETED;
        $this->updateOpLog($comment, '删除回复', trim(request('reason')));
    }

    //审核未通过列表的回复，放到回收站
    public function trash(Comment $comment)
    {
        $comment->status = Comment::TRASH;
        $this->updateOpLog($comment, '回复审核未通过', trim(request('reason')));
    }

    public function edit(Comment $comment)
    {
        $menu = $comment->status == Comment::VISIBLE ? 'index' : 'audit';

        return View::make('dashboard.comments.create_edit')
            ->with('comment', $comment)
            ->with('current_menu', 'comment')
            ->with('current_nav', $menu);
    }

    public function update(Comment $comment)
    {
        //修改回复内容
        $this->validate(request(),[
            'comment.body'   =>     'min:5|max:800',
        ],[
            'comment.body.min' => '内容需5'. '-'.'800个字符',
            'comment.body.max' => '内容需5'. '-'.'800个字符',
        ]);
        $commentData = Input::get('comment');
        $commentData['body_original'] = $commentData['body'];
        try {
            $comment = dispatch( new UpdateCommentCommand($comment, $commentData));

        } catch (\Exception $e) {
            return Redirect::route('dashboard.comment.edit', $comment->id)
                ->withInput($commentData)
                ->withErrors($e->getMessage());
        }

        if ($comment->status == Comment::VISIBLE) {
            return Redirect::route('dashboard.comments.index')->withSuccess('恭喜，操作成功！');
        }
        return Redirect::route('dashboard.comments.audit')->withSuccess('恭喜，操作成功！');
    }

}