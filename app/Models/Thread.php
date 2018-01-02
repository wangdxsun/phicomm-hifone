<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Models;

use AltThree\Validator\ValidatingTrait;
use Carbon\Carbon;
use Config;
use Auth;
use Elasticquent\ElasticquentTrait;
use Hifone\Models\Scopes\Recent;
use Hifone\Models\Traits\Taggable;
use Hifone\Services\Dates\DateFactory;
use Hifone\Services\Tag\TaggableInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Input;
use Venturecraft\Revisionable\RevisionableTrait;

class Thread extends BaseModel implements TaggableInterface
{
    use ValidatingTrait, Taggable, Recent, RevisionableTrait, SoftDeletes, ElasticquentTrait;

    const VISIBLE = 0;//正常帖子
    const TRASH = -1;//审核未通过
    const AUDIT = -2;//待审核 or 审核中
    const DELETED = -3;//已删除

    //发帖渠道channel -1:意见反馈；0:社区
    const FEEDBACK = -1;

    //发帖设备device 0:H5；1：Android；2：iOS；3：Web
    const H5 = 0;
    const ANDROID = 1;
    const IOS = 2;
    const WEB = 3;

    // manually maintain
    public $timestamps = false;

    //use SoftDeletingTrait;
    protected $dates = ['deleted_at', 'last_op_time'];

    protected $fillable = [
        'title',
        'body',
        'excerpt',
        'channel',
        'dev_info',
        'contact',
        'body_original',
        'user_id',
        'node_id',
        'sub_node_id',
        'is_excellent',
        'created_at',
        'updated_at',
        'thumbnails',
        'reply_count',
        'ip',
    ];

    protected $hidden = ['body_original', 'bad_word', 'is_blocked', 'heat_offset', 'follower_count', 'ip',
        'last_op_user_id', 'last_op_reason', 'last_op_time', 'deleted_at'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'title'   => 'required|max:80',
        'body'    => 'required',
        'node_id' => 'required|int',
        'sub_node_id' => 'required|int',
        'user_id' => 'required|int',
        'heat_offset' => 'int',
    ];

    protected $mappingProperties = [
        'title' => [
            'type' => 'string',
            'analyzer' => 'ik_max_word',
        ],
        'body' => [
            'type' => 'string',
            'analyzer' => 'ik_max_word',
        ],
        'created_at' => [
            'type' => 'date',
            'format' => 'yyyy-MM-dd HH:mm',
        ]
    ];
    
    public static $orderTypes = [
        'id'         => '发帖时间',
        'node_id'    => '帖子版块',
        'user_id'    => '发帖人',
        'heat'       => '热度值',
        'updated_at' => '最后回复时间',
        'channel'    => '发帖来源',
        'reply_count'=> '回复数量',
    ];

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function followers()
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'object');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function subNode()
    {
        return $this->belongsTo(SubNode::class);
    }

    public function scopeOfNode($query, Node $node)
    {
        return $query->where('node_id', $node->id);
    }

    public function scopeOfSubNode($query, SubNode $subNode)
    {
        return $query->where('sub_node_id', $subNode->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'username', 'avatar_url', 'role', 'password', 'thread_count', 'score',
            'notification_reply_count', 'notification_at_count', 'notification_system_count', 'notification_chat_count', 'notification_follow_count']);
    }

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    public function lastReplyUser()
    {
        return $this->belongsTo(User::class, 'last_reply_user_id')->select(['id', 'username', 'avatar_url']);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function appends()
    {
        return $this->hasMany(Append::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function generateLastReplyUserInfo()
    {
        $lastReply = $this->replies()->visible()->recent()->first();

        $this->last_reply_user_id = $lastReply ? $lastReply->user_id : 0;
        $this->updated_at = $lastReply->created_time;
        $this->save();
    }

    //帖子详情处判定是否可见
    public function inVisible()
    {
        //未登录，帖子可见性取决于帖子本身
        if (Auth::guest() && !$this->getVisibleAttribute()) {
            return true;
        }
        //已登录，帖子可见取决于帖子状态和是否当前用户或管理员
        return !$this->getVisibleAttribute() && !(Auth::id() == $this->user->id || Auth::user()->can('view_thread'));
    }

    //正常
    public function scopeVisible($query)
    {
        return $query->where('status', '>=', static::VISIBLE);
    }

    //待审核
    public function scopeAudit($query)
    {
        return $query->where('status', static::AUDIT);//审核中
    }

    //回收站
    public function scopeTrash($query)
    {
        return $query->whereIn('status', [static::TRASH, static::DELETED]);//审核未通过和已删除
    }

    //正常和已删除
    public function scopeVisibleAndDeleted($query)
    {
        return $query->whereIn('status', [static::VISIBLE, static::DELETED]);
    }

    public function scopeTitle($query, $search)
    {
        if (!$search) {
            return null;
        }

        return $query->where('title', 'LIKE', "%$search%");
    }

    public function scopeBody($query, $search)
    {
        if (!$search) {
            return null;
        }

        return $query->where('body', 'LIKE', "%$search%");
    }

    /**
     * 边栏同一节点下的话题列表.
     */
    public function getSameNodeThreads($limit = 8)
    {
        return $this->where('node_id', '=', $this->node_id)
            ->recent()
            ->take($limit)
            ->get();
    }

    public function scopeHot($query)
    {
        return $query->orderBy('order', 'desc')->orderBy('heat', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopePinAndRecent($query)
    {
        return $query->orderBy('order', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopeHeat($query)
    {
        return $query->where('heat', '>', -50000)->orWhere('heat', null);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeExcellent($query)
    {
        return $query->where('is_excellent', '=', true);
    }

    public function scopeFeedback($query)
    {
        return $query->where('channel', STATIC::FEEDBACK);
    }

    public static function makeExcerpt($body)
    {
        $html = $body;
        $excerpt = trim(preg_replace('/\s\s+/', ' ', strip_tags($html)));

        return str_limit($excerpt, 200);
    }

    public function replyFloorFromIndex($index)
    {
        $index += 1;
        $current_page = Input::get('page') ?: 1;

        return ($current_page - 1) * Config::get('hifone.replies_perpage') + $index;
    }

    //帖子可见性
    public function getVisibleAttribute()
    {
        return $this->status == static::VISIBLE || $this->status == static::DELETED;
    }

    public function getReportAttribute()
    {
        return $this->title;
    }

    public function getUrlAttribute()
    {
        return route('thread.show', $this->id);
    }

    public function isFollowedBy($user)
    {
        return $this->followers()->forUser($user->id)->count() > 0;
    }

    public function isFavoritedBy($user)
    {
        return $this->favorites()->forUser($user->id)->count() > 0;
    }

    public function getPinAttribute()
    {
        return $this->order > 0 ? 'fa fa-thumb-tack text-danger' : 'fa fa-thumb-tack';
    }

    public function getExcellentAttribute()
    {
        return $this->is_excellent ? 'fa fa-diamond text-danger' : 'fa fa-diamond';
    }

    public function getSinkAttribute()
    {
        return $this->order < 0 ? 'fa fa-anchor text-danger' : 'fa fa-anchor';
    }

    public function getHighlightAttribute()
    {
        return (Carbon::now()->format('Ymd') == app(DateFactory::class)->make($this->updated_time)->format('Ymd')) ? 'text-danger' : null;
    }

    public function getIconsAttribute()
    {
        $icons = [];
        $this->is_excellent && $icons[] = 'fa fa-diamond text-danger';
        $this->order > 0 && $icons[] = 'fa fa-thumb-tack text-danger';
        return $icons;
    }

    public function scopeSearch($query, $searches = [])
    {
        foreach ($searches as $key => $value) {
            if ($key == 'user_id') {
                $query->whereHas('user', function ($query) use ($value){
                    $query->where('username', 'like',"%$value%");
                });
            } elseif ($key == 'body') {
                $value = app('parser.markdown')->convertMarkdownToHtml(app('parser.at')->parse($value));
                $value = substr($value, 3, sizeof($value)-5);
                $query->where('body', 'LIKE', "%$value%");
            } elseif ($key == 'title') {
                $value = app('parser.markdown')->convertMarkdownToHtml(app('parser.at')->parse($value));
                $value = substr($value, 3, sizeof($value)-5);
                $query->where('title', 'LIKE', "%$value%");
            } elseif ($key == 'date_start') {
                $query->where('created_at', '>=', $value);
            } elseif ($key == 'date_end') {
                $query->where('created_at', '<=', $value);
            } elseif ($key == 'orderType'){
                if ($value == 'reply_count') {
                    $query->orderBy($value);
                }
                $query->orderBy($value,'desc');
            } elseif ($key == 'channel') {
                $query->where('channel', '=', $value);
            } else {
                $query->where($key, $value);
            }
        }
    }

    public function getExcerptAttribute($value)
    {
        return $value ?: '点击查看更多';
    }

    //动态计算热度值
    public function getHeatComputeAttribute()
    {
        $excellent_score = Config::get('setting.excellent_score', 10000);
        $view_score = Config::get('setting.view_score', 1);
        $like_score = Config::get('setting.like_score', 20);
        $reply_score = Config::get('setting.reply_score', 50);
        $time_score = Config::get('setting.time_score', 1000);

        $excellent = $this->is_excellent != 0 ? $excellent_score : 0;

        $createAt = new Carbon($this['attributes']['created_at']);
        $now = Carbon::now();
        $timeAlive = $now->diffInSeconds($createAt);

        $heat = $this->view_count * $view_score + $this->like_count * $like_score + $this->reply_count * $reply_score + $excellent
            + $this->heatCoolingValue($timeAlive, $time_score) + $this->heat_offset;
        $heat = ($heat > -100000) ? $heat : -100000;
        return round($heat, 2, PHP_ROUND_HALF_DOWN);
    }

    private function heatCoolingValue($timeAlive, $timeScore)
    {
        //72小时后逐渐降低，72小时为中点
        if ($timeAlive <= 72 * 60 * 60) {
            return $timeScore * cos($timeAlive * PI() / (60 * 60 * 72 * 2));
        } elseif ($timeAlive <= 72 * 2 * 60 * 60) {
            return 5 * $timeScore * cos($timeAlive * PI() / (60 * 60 * 72 * 2));
        } else {
            $score = ($timeAlive - 60 * 60 * 72 * 2) * (-0.1) - 5 * $timeScore;
            return $score > -300000 ? $score : -300000;
        }
    }

    //计算帖子中自己的评论数
    public function selfReplyCount(Thread $thread)
    {
       return $this->replies()->visible()->where('user_id',$thread->user_id)->count();
    }
    //计算帖子自己点赞数
    public function selfLikeCount(Thread $thread)
    {
        return $this->likes()->where('user_id',$thread->user_id)->count();
    }
    //计算帖子自己收藏数
    public function selfFavoriteCount(Thread $thread)
    {
        return $this->favorites()->where('user_id',$thread->user_id)->count();
    }

    public function getDevInfoAttribute($value)
    {
        if (!$value) {
            return [];
        }
        if (!is_array($value)) {
            return json_decode($value, true);
        }
        return $value;
    }

//    public function getDeviceAttribute($value)
//    {
//        switch ($value) {
//            case Thread::H5:
//                return 'H5';
//            case Thread::ANDROID:
//                return 'Android';
//            case Thread::IOS:
//                return 'iOS';
//            case Thread::WEB:
//                return 'Web';
//            default:
//                return '未知';
//        }
//    }
}
