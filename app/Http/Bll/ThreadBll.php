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
use Hifone\Events\Thread\ThreadWasViewedEvent;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
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
        $threads = Thread::visible()->title(request('q'))->with(['user', 'node'])->recent()->paginate();

        return $threads;
    }

    public function createThread()
    {
        $threadData = Input::get('thread');
        $node_id = isset($threadData['node_id']) ? $threadData['node_id'] : null;
        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';
        $images = '';

        //如果有单独上传图片，将图片拼接到正文后面
//        if (Input::hasFile('images')) {
//            foreach ($images = Input::file('images') as $image) {
//                $res = dispatch(new UploadImageCommand($image));
//                $threadData['body'] .= "<img src='{$res["filename"]}'/>";
//            }
//        }

        //base64上传
        if (Input::has('images')) {
            foreach (Input::get('images') as $image) {
                $upload = dispatch(new UploadBase64ImageCommand($image));
                $images .= "<img src='{$upload["filename"]}'/>";
            }
        }

        dispatch(new AddThreadCommand(
            $threadData['title'],
            $threadData['body'],
            Auth::id(),
            $node_id,
            $tags,
            $images
        ));
    }

    public function showThread($thread)
    {
        if ($thread->inVisible()) {
            throw new NotFoundHttpException('帖子状态不可见');
        }
        event(new ThreadWasViewedEvent($thread));

        $thread = $thread->load(['user', 'node']);
        $replies = $this->replies($thread);
        $thread['followed'] = User::hasFollowUser($thread->user);
        $thread['liked'] = Auth::check() ? Auth::user()->hasLikeThread($thread) : false;
        $thread['replies'] = $replies;

        return $thread;
    }

    public function replies($thread)
    {
        $replies = $thread->replies()->visible()->with(['user'])
            ->orderBy('order', 'desc')->orderBy('created_at', 'desc')->paginate();
        foreach ($replies as &$reply) {
            $reply['liked'] = Auth::check() ? Auth::user()->hasLikeReply($reply) : false;
        }

        return $replies;
    }
}