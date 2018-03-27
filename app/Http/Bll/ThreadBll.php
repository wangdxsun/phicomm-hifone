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
use Hifone\Exceptions\HifoneException;
use Hifone\Models\SearchWord;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use DB;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Pagination\Paginator;
use Input;
use Auth;
use Config;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Agent;

class ThreadBll extends BaseBll
{
    public function getThreads()
    {
        (new CommonBll())->loginWeb();

        $repository = app('repository');
        $repository->pushCriteria(new Filter(Input::query('filter')));
        $repository->pushCriteria(new Search(Input::query('q')));
        $threads = $repository->model(Thread::class)->getThreadList();

        return $threads;
    }

    public function search($keyword)
    {
        if (empty($keyword)) {
            $threads = new Paginator([], 15);
        } else {
            $this->searchWords($keyword);
            $threads = Thread::searchThread($keyword)->load(['user', 'node'])->paginate(15);
        }

        return $threads;
    }

    public function webSearch($keyword)
    {
        if (empty($keyword)) {
            $threads = new Paginator([], 0, 15);
        } else {
            $threads = Thread::searchThread($keyword);
            $this->searchWords($keyword);
            foreach ($threads as $thread) {
                unset($thread['node']);
                unset($thread['user']);
            }
            $threads = $threads->load(['user', 'node', 'lastReplyUser']);
        }

        return $threads;
    }


    //记录搜索词
    public function searchWords($word)
    {
        if (Redis::sAdd(substr(date('Ymd'),1,10), $word)) {
            Redis::expire(substr(date('Ymd'),1,10), 60*60*24);
            $data = [
                'word'  => $word,
                'created_at' => Carbon::today()->toDateString(),
                'updated_at' => Carbon::today()->toDateString(),
                'count'      => 1,
                'stat_count' =>  SearchWord::where('word', $word)->max('stat_count') + 1,
            ];
            SearchWord::create($data);
        } else {
            SearchWord::where('word', $word)->where('created_at',Carbon::today()->toDateString())->increment('count', 1);
            SearchWord::where('word', $word)->where('created_at',Carbon::today()->toDateString())->increment('stat_count', 1);
        }
        return ;
    }

    //H5端发帖图文分开 Web发帖富文本图文混排
    public function createThread($threadData)
    {
        $node_id = SubNode::find($threadData['sub_node_id'])->node_id;

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
            $threadData['sub_node_id'],
            $tags,
            $images
        ));

        $thread = Thread::find($threadTemp->id);
        return $thread;
    }

    //APP发帖支持图文混排
    public function createThreadImageMixed()
    {
        $threadData = Input::get('thread');
        $sub_node_id = isset($threadData['sub_node_id']) ? $threadData['sub_node_id'] : null;
        $node_id = SubNode::find($sub_node_id)->node_id;
        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';
        $json_bodies = json_decode($threadData['body'], true);
        $body = '';
        foreach ($json_bodies as $json_body) {
            if ($json_body['type'] == 'text') {
                $body.= "<p>".e($json_body['content'])."</p>";
            } elseif ($json_body['type'] == 'image') {
                $body.= "<img src='".$json_body['content']."'/>";
            }
        }
        if (mb_strlen(strip_tags($body)) > 10000) {
            throw new HifoneException('帖子内容不得多于10000个字符');
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

    public function createFeedback()
    {
        $threadData = Input::get('thread');
        $sub_node_id = isset($threadData['sub_node_id']) ? $threadData['sub_node_id'] : null;
        $node_id = SubNode::find($sub_node_id)->node_id;
        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';
        $images = '';
        if (Input::has('images')) {
            foreach ($threadImages = json_decode(Input::get('images'), true) as $image) {
                $images.= "<img src='".$image['image']."'/>";
            }
        }

        $channel = Thread::FEEDBACK;
        $dev_info = $threadData['dev_info'];
        $contact  = isset($threadData['contact']) ? $threadData['contact'] : null;

        $threadTemp = dispatch(new AddThreadCommand(
            $threadData['title'],
            $threadData['body'],
            Auth::id(),
            $node_id,
            $sub_node_id,
            $tags,
            $images,
            $channel,
            $dev_info,
            $contact
        ));
        $thread = Thread::find($threadTemp->id);
        return $thread;
    }

    public function auditThread(Thread $thread, WordsFilter $wordsFilter)
    {
        $thread->heat = $thread->heat_compute;
        $post = $thread->title.$thread->body;
        $badWord = '';
        if (Config::get('setting.auto_audit', 0) == 0 || ($badWord = $wordsFilter->filterWord($post)) || $this->isContainsImageOrUrl($post)) {
            $thread->bad_word = $badWord;
            $msg = '帖子已提交，待审核';
        } else {
            $this->autoAudit($thread);
            $msg = '发布成功';
        }
        $thread->body = app('parser.at')->parse($thread->body);
        $thread->body = app('parser.emotion')->parse($thread->body);
        //只有H5和app发帖需要自动转义链接，web端不需要
        if (Agent::match('iPhone') || Agent::match('Android')) {
            $thread->body = app('parser.link')->parse($thread->body);
        }
        $thread->save();
        return [
            'msg' => $msg,
            'thread' => $thread
        ];
    }

    public function showThread(Thread $thread)
    {
        if ($thread->inVisible()) {
            throw new HifoneException('该帖子已被删除', 410);
        }
        event(new ThreadWasViewedEvent(clone $thread));

        $thread = $thread->load(['user', 'node']);
        $thread['followed'] = User::hasFollowUser($thread->user);
        $thread['liked'] = Auth::check() ? Auth::user()->hasLikeThread($thread) : false;
        $thread['reported'] = Auth::check() ? Auth::user()->hasReportThread($thread) : false;
        $thread['favorite'] = Auth::check() ? Auth::user()->hasFavoriteThread($thread) : false;

        return $thread;
    }

    public function replies(Thread $thread, $source = '')
    {
        if ($source == 'web') {
            $replies = $thread->replies()->visible()->with(['user', 'reply.user'])->pinAndRecent()->paginate();
        } else {
            $replies = $thread->replies()->visibleAndDeleted()->with(['user', 'reply.user'])->pinAndRecent()->paginate();
        }
        foreach ($replies as $reply) {
            $reply['liked'] = Auth::check() ? Auth::user()->hasLikeReply($reply) : false;
            $reply['reported'] = Auth::check() ? Auth::user()->hasReportReply($reply) : false;
        }
        return $replies;
    }

    public function autoAudit(Thread $thread)
    {
        //自动审核通过，触发相应的代码逻辑
        event(new ThreadWasAddedEvent($thread));
        DB::beginTransaction();
        try {
            $thread->status = Thread::VISIBLE;
            $this->updateOpLog($thread, '自动审核通过');
            $thread->addToIndex();
            $thread->node->update(['thread_count' => $thread->node->threads()->visible()->count()]);
            if ($thread->subNode) {
                $thread->subNode->update(['thread_count' => $thread->subNode->threads()->visible()->count()]);
            }
            $thread->user->update(['thread_count' => $thread->user->threads()->visibleAndDeleted()->count()]);
            event(new ThreadWasAuditedEvent($thread));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


}