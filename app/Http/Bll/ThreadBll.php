<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2017/5/3
 * Time: 16:11
 */

namespace Hifone\Http\Bll;

use Hifone\Commands\Image\UploadBase64ImageCommand;
use Hifone\Commands\Thread\AddThreadCommand;
use Hifone\Events\Thread\ThreadWasAddedEvent;
use Hifone\Events\Thread\ThreadWasAuditedEvent;
use Hifone\Events\Thread\ThreadWasViewedEvent;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use DB;
use Input;
use Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThreadBll extends BaseBll
{
    public function getThreads()
    {
        (new CommonBll())->login();

        $repository = app('repository');
        $repository->pushCriteria(new Filter(Input::query('filter')));
        $repository->pushCriteria(new Search(Input::query('q')));
        $threads = $repository->model(Thread::class)->getThreadList();

        return $threads;
    }

    public function search()
    {
        $threads = Thread::searchThread(request('q'));
        foreach ($threads as $thread) {
            unset($thread['node']);
            unset($thread['user']);
        }
        $threads = $threads->load(['user', 'node', 'lastReplyUser']);

        return $threads;
    }

    public function createThread()
    {
        $threadData = Input::get('thread');
        $sub_node_id = isset($threadData['sub_node_id']) ? $threadData['sub_node_id'] : null;
        $node_id = SubNode::find($sub_node_id)->node_id;

        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';
        $images = '';

        //base64上传
        if (Input::has('images')) {
            foreach ($threadImages = Input::get('images') as $image) {
                $upload = dispatch(new UploadBase64ImageCommand($image));
                $images .= "<img src='{$upload["filename"]}'/>";
            }
        }
        $threadTemp = dispatch(new AddThreadCommand(
            $threadData['title'],
            $threadData['body'],
            Auth::id(),
            $node_id,
            $sub_node_id,
            $tags,
            $images
        ));

        $thread = Thread::find($threadTemp->id);
        return $thread;
    }

    public function createThreadInApp()
    {
        $threadData = Input::get('thread');
        $sub_node_id = isset($threadData['sub_node_id']) ? $threadData['sub_node_id'] : null;
        $node_id = SubNode::find($sub_node_id)->node_id;
        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';
        $json_bodies = json_decode($threadData['body'], true);
        $body = '';
        foreach ($json_bodies as $json_body) {
            if ($json_body['type'] == 'text') {
                $body.= "<p>".$json_body['content']."</p>";
            } elseif ($json_body['type'] == 'image') {
                $body.= "<img src='".$json_body['content']."'/>";
            }
        }

        $threadTemp = dispatch(new AddThreadCommand(
            $threadData['title'],
            $body,
            Auth::id(),
            $node_id,
            $sub_node_id,
            $tags
        ));
        $thread = Thread::find($threadTemp->id);
        return $thread;
    }

    public function showThread($thread)
    {
        if ($thread->inVisible()) {
            throw new NotFoundHttpException('帖子状态不可见');
        }
        event(new ThreadWasViewedEvent($thread));

        $thread = $thread->load(['user', 'node']);
        $thread['followed'] = User::hasFollowUser($thread->user);
        $thread['liked'] = Auth::check() ? Auth::user()->hasLikeThread($thread) : false;
        $thread['favorite'] = Auth::check() ? Auth::user()->hasFavoriteThread($thread) : false;
        $thread['reported'] = Auth::check() ? Auth::user()->hasReportThread($thread) : false;

        return $thread;
    }

    public function replies($thread)
    {
        $replies = $thread->replies()->visible()->with(['user', 'reply.user'])->pinAndRecent()->paginate();
        foreach ($replies as &$reply) {
            $reply['liked'] = Auth::check() ? Auth::user()->hasLikeReply($reply) : false;
        }
        return $replies;
    }

    public function AutoAudit($thread)
    {
        //自动审核通过，触发相应的代码逻辑
        event(new ThreadWasAddedEvent($thread));
        DB::beginTransaction();
        try {
            $thread->status = 0;
            $thread->addToIndex();
            $this->updateOpLog($thread, '自动审核通过');
            $thread->node->update(['thread_count' => $thread->node->threads()->visible()->count()]);
            if ($thread->subNode) {
                $thread->subNode->update(['thread_count' => $thread->subNode->threads()->visible()->count()]);
            }
            $thread->user->update(['thread_count' => $thread->user->threads()->visible()->count()]);
            event(new ThreadWasAuditedEvent($thread));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}