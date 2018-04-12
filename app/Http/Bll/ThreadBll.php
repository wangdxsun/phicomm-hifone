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

    public function search($keyword, $recent = null)
    {
        if (empty($keyword)) {
            $threads = new Paginator([], 15);
        } else {
            $this->searchWords($keyword);
            $threads = Thread::searchThread($keyword, $recent)->load(['user', 'node'])->paginate(15);
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
        return;
    }

    //发帖 H5端:图文分开 Web:富文本图文混排
    public function createThread($threadData)
    {
        $node_id = SubNode::find($threadData['sub_node_id'])->node_id;
        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';

        //base64上传 兼容H5
        $images = '';
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
        if (1 == array_get($threadData, 'is_vote')) {
            $threadTemp->update([
                'is_vote' => 1,
                'option_max' => array_get($threadData, 'option_max', 1),
                'vote_start' => $threadData['vote_start'],
                'vote_end' => $threadData['vote_end'],
                'vote_level' => array_get($threadData, 'vote_level', 0),
                'view_voting' => array_get($threadData,'view_voting', Thread::VOTE_ONLY),
                'view_vote_finish' => array_get($threadData,'view_vote_finish', Thread::VOTE_ONLY)
            ]);

            //创建投票选项
            $contents = $threadData['options'];
            foreach ($contents as $key => $content) {
                Option::create([
                    'thread_id' => $threadTemp->id,
                    'order' => $key + 1,
                    'content' => $content
                ]);
            }
        }
        $thread = Thread::find($threadTemp->id);

        return $thread;
    }

    //web创建草稿
    public function createDraft($threadData)
    {
        //草稿可以不填版块和标题信息
        $node_id = null;
        if (isset($threadData['sub_node_id'])) {
            $node_id = SubNode::find($threadData['sub_node_id'])->node_id;
        }
        $tags = isset($threadData['tags']) ? $threadData['tags'] : '';

        $images = '';
        //草稿贴标题、版块信息可为空
        $threadTemp = dispatch(new AddThreadCommand(
            array_get($threadData, 'title'),
            $threadData['body'],
            Auth::id(),
            $node_id,
            array_get($threadData, 'sub_node_id'),
            $tags,
            $images,
            $threadData['status']
        ));

        //投票贴存为草稿
        if (1 == array_get($threadData, 'is_vote')) {
            $threadTemp->update([
                'is_vote' => 1,
                'option_max' => array_get($threadData, 'option_max', 1),
                'vote_start' => array_get($threadData, 'vote_start'),
                'vote_end' => array_get($threadData, 'vote_end'),
                'vote_level' => array_get($threadData, 'vote_level', 0),
                'view_voting' => array_get($threadData,'view_voting', Thread::VOTE_ONLY),
                'view_vote_finish' => array_get($threadData,'view_vote_finish', Thread::VOTE_ONLY)
            ]);

            //创建投票选项(可以不填)
            if (null <> $contents = array_get($threadData, 'options')) {
                foreach ($contents as $key => $content) {
                    Option::create([
                        'thread_id' => $threadTemp->id,
                        'order' => $key + 1,
                        'content' => $content
                    ]);
                }
            }
        }

        $thread = Thread::find($threadTemp->id);
        return $thread;
    }

    //编辑草稿继续存为草稿
    public function updateDraft(Thread $thread, $threadData)
    {
        //草稿贴标题、版块信息可为空
        $node_id = null;
        if (isset($threadData['sub_node_id'])) {
            $node_id = SubNode::find($threadData['sub_node_id'])->node_id;
        }
        $updateData['node_id'] = $node_id;
        $updateData['title'] = array_get($threadData, 'title');
        $updateData['body'] = $threadData['body'];
        $updateData['sub_node_id'] = array_get($threadData, 'sub_node_id');
        //更新草稿贴编辑时间
        $updateData['edit_time'] = Carbon::now()->toDateTimeString();
        $thread->update($updateData);

        //是投票贴
        if (1 == $thread->is_vote) {
            $thread->update([
                'is_vote' => 1,
                'option_max' => array_get($threadData, 'option_max', 1),
                'vote_start' => array_get($threadData, 'vote_start'),
                'vote_end' => array_get($threadData, 'vote_end'),
                'vote_level' => array_get($threadData, 'vote_level', 0),
                'view_voting' => array_get($threadData,'view_voting', Thread::VOTE_ONLY),
                'view_vote_finish' => array_get($threadData,'view_vote_finish', Thread::VOTE_ONLY)
            ]);

            //编辑投票选项并固化(新的完全替换旧的，可以不填)
            $thread->options()->delete();
            if (null <> $contents = array_get($threadData, 'options')) {
                foreach ($contents as $key => $content) {
                    Option::create([
                        'thread_id' => $thread->id,
                        'order' => $key + 1,
                        'content' => $content
                    ]);
                }
            }
        }

        return $thread;
    }

    //发布草稿为帖子
    public function makeDraftToThread(Thread $thread, $threadData)
    {
        //草稿状态变为待审核
        $updateData['status'] = Thread::AUDIT;
        $updateData['node_id'] = SubNode::find($threadData['sub_node_id'])->node_id;
        $updateData['title'] = $threadData['title'];
        $updateData['body'] = $threadData['body'];
        $updateData['sub_node_id'] = $threadData['sub_node_id'];
        $thread->update($updateData);

        //发布的是投票贴
        if (1 == $thread->is_vote) {
            $thread->update([
                'is_vote' => 1,
                'option_max' => array_get($threadData, 'option_max', 1),
                'vote_start' => $threadData['vote_start'],
                'vote_end' => $threadData['vote_end'],
                'vote_level' => array_get($threadData, 'vote_level', 0),
                'view_voting' => array_get($threadData,'view_voting', Thread::VOTE_ONLY),
                'view_vote_finish' => array_get($threadData,'view_vote_finish', Thread::VOTE_ONLY)
            ]);

            //编辑投票选项并固化(新的完全替换旧的)
            $thread->options()->delete();
            if (null == $contents = array_get($threadData, 'options')) {
                throw new HttpException('请填写选项信息');
            }
            foreach ($contents as $key => $content) {
                Option::create([
                    'thread_id' => $thread->id,
                    'order' => $key + 1,
                    'content' => $content
                ]);
            }
        }
        $thread = Thread::find($thread->id);

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
        //新增判断逻辑：不具有免审核权限的用户才需要自动审核
        if (!Auth::user()->can('free_audit')
            && Config::get('setting.auto_audit', 0) == 0
            || ($badWord = $wordsFilter->filterWord($post))
            || $this->isContainsImageOrUrl($post)) {
            $thread->bad_word = $badWord;
        } else {
            $this->autoAudit($thread);
        }
        $thread->body = app('parser.at')->parse($thread->body);
        $thread->body = app('parser.emotion')->parse($thread->body);
        //只有H5和app发帖需要自动转义链接，web端不需要
        if (Agent::match('iPhone') || Agent::match('Android')) {
            $thread->body = app('parser.link')->parse($thread->body);
        }
        $thread->save();
        return $thread;
    }

    //帖子详情
    public function showThread(Thread $thread)
    {
        if (!$thread->isVisible()) {
            throw new HifoneException('该帖子已被删除', 410);
        }
        event(new ThreadWasViewedEvent(clone $thread));
        $thread = $thread->load(['user', 'node']);

        if ($thread->is_vote == 1) {//投票贴
            $thread = $thread->load(['options']);
            foreach ($thread['options'] as $option) {
                $option['voted'] = Auth::check() ? Auth::user()->hasVoteOption($option) : false;
            }
            $thread['view_vote'] = $this->canViewVote($thread);
            $thread['voted'] = $this->isVoted($thread);
            $thread['now'] = Carbon::now()->toDateTimeString();
        }

        $thread['followed'] = User::hasFollowUser($thread->user);
        $thread['liked'] = Auth::check() ? Auth::user()->hasLikeThread($thread) : false;
        $thread['reported'] = Auth::check() ? Auth::user()->hasReportThread($thread) : false;
        $thread['favorite'] = Auth::check() ? Auth::user()->hasFavoriteThread($thread) : false;
        $thread['edited'] = $thread->created_time < $thread->edit_time ? true : false;

        return $thread;
    }

    //是否已投票
    public function isVoted(Thread $thread)
    {
        if (Auth::check() && Auth::user()->hasVoteThread($thread)) {//已投票
            return true;
        } else {
            return false;//投票按钮可点击
        }
    }

    /**
     * 是否可以查看投票结果
     * 判断逻辑
     *
     * 管理员则可见，否则
     * 投票中/投票结束
     * 分别讨论各结果可见性（1仅投票可见，2仅回复可见，3投票和回复可见，4所有人可见，5只有管理员可见）
     * （是否投票过，是否回复过（不需要审核通过））
     */
    public function canViewVote(Thread $thread)
    {
        if (Carbon::now()->toDateTimeString() < $thread->vote_start) {
            return false;
        } elseif ($thread->vote_start <= Carbon::now()->toDateTimeString()
            && Carbon::now()->toDateTimeString() <= $thread->vote_end) {//投票中
            if ($thread->view_voting == Thread::VOTE_ONLY) {//1仅投票可见
                if (Auth::check() && Auth::user()->hasVoteThread($thread)) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($thread->view_voting == Thread::REPLY_ONLY) {//2仅回复可见
                if (Auth::check() && Auth::user()->hasCommentThread($thread)) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($thread->view_voting == Thread::VOTE_ONLY + Thread::REPLY_ONLY) {//3投票和回复可见
                if (Auth::check() && (Auth::user()->hasCommentThread($thread) || Auth::user()->hasVoteThread($thread))) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($thread->view_voting == Thread::ALL) {//4所有人可见
                return true;
            } else {//5只有管理员可见
                if (Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {//投票结束后
            if ($thread->view_vote_finish == Thread::VOTE_ONLY) {//1仅投票可见
                if (Auth::check() && Auth::user()->hasVoteThread($thread)) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($thread->view_vote_finish == Thread::REPLY_ONLY) {//2仅回复可见
                if (Auth::check() && Auth::user()->hasCommentThread($thread)) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($thread->view_vote_finish == Thread::VOTE_ONLY + Thread::REPLY_ONLY) {//3投票和回复可见
                if (Auth::check() && (Auth::user()->hasCommentThread($thread) || Auth::user()->hasVoteThread($thread))) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($thread->view_vote_finish == Thread::ALL) {//4所有人可见
                return true;
            } else {//5只有管理员可见
                if (Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Founder'))) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    public function sortReplies(Thread $thread, $sort, $source = 'app')
    {
        //四种排序方式 pinAndRecent：兼容旧版本 like：点赞最多  desc：时间逆序  asc:时间正序
        if ($source == 'web') {//web端查看评论列表只显示状态正常的
            switch ($sort) {
                case 'pinAndRecent':
                    $replies = $thread->replies()->visible()->with(['user', 'reply.user'])->pinAndRecent()->paginate();
                    break;
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
                case 'pinAndRecent':
                    $replies = $thread->replies()->visibleAndDeleted()->with(['user', 'reply.user'])->pinAndRecent()->paginate();
                    break;
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
            $threadForIndex = clone $thread;
            $threadForIndex->addToIndex();
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
        if ($this->isVoted($thread)) {
            throw new HifoneException('您已投票，请勿重复投票');
        }
        if (!$this->canVote($thread)) {
            throw new HifoneException('你的级别不可参与此次投票');
        }
        if (Carbon::now()->toDateTimeString() < $thread->vote_start) {
            throw new HifoneException('投票还未开始');
        } elseif (Carbon::now()->toDateTimeString() > $thread->vote_end) {
            throw new HifoneException('投票已结束');
        } else {
            //用户投票选择了
            $select = request('votes');
            $optionIds = explode(',', $select);
            $options = Option::whereIn('id',$optionIds)->where('thread_id',$thread->id)->get();
            if ($thread->option_max < count($options)) {
                throw new HttpException('选项数超过上限');
            } elseif (0 == count($optionIds)) {
                throw new HttpException('选项数不足');
            }
            foreach ($options as $option) {
                OptionUser::create([
                    'option_id' => $option->id,
                    'user_id' => Auth::id(),
                    'thread_id' => $thread->id
                ]);
                $option->increment('vote_count', 1);
            }
            $thread->increment('vote_count');
        }
    }

    private function canVote(Thread $thread)
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

    public function viewVoteResult(Thread $thread, Option $option)
    {
        if ($option->exists) {
            $users = $option->users()->paginate(14);
        } else {
            //所有投该帖的用户，按投票时间逆序，并携带他的投票选项信息
            $users = $thread->voteUsers()->with(['options' => function ($query) use ($thread) {
                $query->wherePivot('thread_id', $thread->id);
            }])->orderBy('created_at', 'desc')->paginate(14);
            foreach ($users as $user) {
                $user['selects'] = $this->selects($user['options']->toArray());
                unset($user['options']);
            }
        }

        return $users;
    }

    private function selects($options)
    {
        $selects = '';
        for ($key= 0; $key < count($options); $key++) {
            if (0 == $key) {
                $selects .= $options[$key]['order'];
            } else {
                $selects = $selects . '/' . $options[$key]['order'];
            }
        }

        return $selects;
    }
}