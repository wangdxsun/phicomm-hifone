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
use Hifone\Models\Option;
use Hifone\Models\OptionUser;
use Hifone\Models\Role;
use Hifone\Models\SearchWord;
use Hifone\Models\SubNode;
use Hifone\Models\Thread;
use Hifone\Models\User;
use Hifone\Repositories\Criteria\Thread\Filter;
use Hifone\Repositories\Criteria\Thread\Search;
use DB;
use Hifone\Services\Filter\WordsFilter;
use Illuminate\Foundation\Testing\HttpException;
use Illuminate\Pagination\Paginator;
use Input;
use Auth;
use Config;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        //base64上传 兼容H5
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

        //发布投票贴
        if (isset($threadData['is_vote']) && 1 == $threadData['is_vote']) {
            $threadTemp->update([
                'is_vote' => 1,
                'option_max' => isset($threadData['option_max']) ? $threadData['option_max'] : 1,
                'vote_start' => $threadData['vote_start'],
                'vote_end' => $threadData['vote_end'],
                'vote_level' => isset($threadData['vote_level']) ? $threadData['vote_level'] : null,
                'vote_voting' => isset($threadData['vote_voting']) ? $threadData['vote_voting'] : 1,
                'vote_vote_finish' => isset($threadData['vote_vote_finish']) ? $threadData['vote_vote_finish'] : 1
            ]);

            //添加投票选项操作
            $contents = $threadData['options'];
            $order = 1;
            foreach ($contents as $content) {
                Option::create([
                    'thread_id' => $threadTemp->id,
                    'order' => $order,
                    'content' => $content
                ]);
                $order++;
            }
        }

        $thread = Thread::find($threadTemp->id);
        return $thread;
    }

    //web保存草稿
    public function createDraft($threadData)
    {
        $node_id = '';
        if (isset($threadData['sub_node_id'])) {
            $node_id = SubNode::find($threadData['sub_node_id'])->node_id;
        }

        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';
        $images = '';

        $threadTemp = dispatch(new AddThreadCommand(
            $threadData['title'],
            $threadData['body'],
            Auth::id(),
            $node_id,
            $threadData['sub_node_id'],
            $tags,
            $images,
            $threadData['status']
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
            Thread::AUDIT,
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
        $thread->body = app('parser.at')->parse($thread->body);
        $thread->body = app('parser.emotion')->parse($thread->body);
        //新增判断逻辑：不具有免审核权限的用户才需要自动审核
        if (!Auth::user()->can('free_audit') && Config::get('setting.auto_audit', 0) == 0 || ($badWord = $wordsFilter->filterWord($post)) || $this->isContainsImageOrUrl($post)) {
            $thread->bad_word = $badWord;
        } else {
            $this->autoAudit($thread);
        }
        $thread->save();
        return $thread;
    }

    public function showThread(Thread $thread)
    {
        if (!$thread->isVisible()) {
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

    public function replies(Thread $thread, $sort = 'desc', $source = '')
    {
        //三种排序方式 like：点赞最多  desc：时间逆序  asc:时间正序
        if ($source == 'web') {//web端查看评论列表只显示状态正常的
            switch ($sort) {
                case 'like':
                    $replies = $thread->replies()->visible()->with(['user', 'reply.user'])->pinLikeAndRecent()->paginate();
                    break;
                case 'desc':
                    $replies = $thread->replies()->visible()->with(['user', 'reply.user'])->recentDesc()->paginate();
                    break;
                case 'asc':
                    $replies = $thread->replies()->visible()->with(['user', 'reply.user'])->recentAsc()->paginate();
                    break;
                default :
                    $replies = $thread->replies()->visible()->with(['user', 'reply.user'])->pinLikeAndRecent()->paginate();
            }
        } else {
            switch ($sort) {
                case 'like':
                    $replies = $thread->replies()->visibleAndDeleted()->with(['user', 'reply.user'])->pinLikeAndRecent()->paginate();
                    break;
                case 'desc':
                    $replies = $thread->replies()->visibleAndDeleted()->with(['user', 'reply.user'])->recentDesc()->paginate();
                    break;
                case 'asc':
                    $replies = $thread->replies()->visibleAndDeleted()->with(['user', 'reply.user'])->recentAsc()->paginate();
                    break;
                default :
                    $replies = $thread->replies()->visibleAndDeleted()->with(['user', 'reply.user'])->pinLikeAndRecent()->paginate();
            }
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

    public function vote(Thread $thread)
    {
        if (!$this->canVote()) {
            throw new HifoneException('对不起，你的级别不可参与此次投票');
        }
        if (Carbon::now < $thread->vote_start) {
            throw new HifoneException('投票还未开始');
        } elseif (Carbon::now > $thread->vote_end) {
            throw new HifoneException('投票已结束');
        } else {
            //用户投票选择了
            $select = request('votes');
            $optionIds = explode(',', $select);
            if (option_max < sizeof($optionIds)) {
                throw new HttpException('选项数超过上限');
            }
            $options = Option::whereIn('id',$optionIds)->get();
            foreach ($options as $option) {
                OptionUser::create(['option_id' => $option->id, 'user_id' => Auth::id()]);
                $option->increment('vote_count', 1);
            }
        }
    }

    private function canVote($thread)
    {
        if (empty($thread->vote_level)) {//All
            return true;
        } else {
            $role = Role::find($thread->vote_level);
            if (Auth::user()->score >= $role->credit_low) {
                return true;
            } else {
                return false;
            }
        }
    }
}