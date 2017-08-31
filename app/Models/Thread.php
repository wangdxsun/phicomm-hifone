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
use Hifone\Models\Scopes\Recent;
use Hifone\Models\Traits\Taggable;
use Hifone\Services\Dates\DateFactory;
use Hifone\Services\Tag\TaggableInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Input;
use Venturecraft\Revisionable\RevisionableTrait;

class Thread extends BaseModel implements TaggableInterface
{
    use ValidatingTrait, Taggable, Recent, RevisionableTrait, SoftDeletes;

    const VISIBLE = 0;//正常帖子
    const TRASH = -1;//回收站
    const Audit = -2;//待审核

    // manually maintain
    public $timestamps = false;

    //use SoftDeletingTrait;
    protected $dates = ['deleted_at', 'last_op_time'];

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'body',
        'excerpt',
        'body_original',
        'user_id',
        'node_id',
        'sub_node_id',
        'is_excellent',
        'created_at',
        'updated_at',
        'thumbnails',
        'ip',
    ];

    protected $hidden = ['body_original', 'bad_word', 'is_blocked', 'heat_offset', 'heat', 'follower_count', 'ip', 'last_reply_user_id',
        'last_op_user_id', 'last_op_reason', 'last_op_time', 'deleted_at'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'title'   => 'required|min:1|max:80',
        'body'    => 'required',
        'node_id' => 'required|int',
        'sub_node_id' => 'required|int',
        'user_id' => 'required|int',
        'heat_offset' => 'int',
    ];
    
    public static $orderTypes = [
        'id' => '发帖时间',
        'node_id' => '帖子板块',
        'user_id'  => '发帖人',
        'heat'  => '热度值',
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
        return $this->belongsTo(Node::class)->select(['id', 'name']);
    }

    public function scopeOfNode($query, Node $node)
    {
        return $query->where('node_id', $node->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'username', 'avatar_url', 'role']);
    }

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    public function lastReplyUser()
    {
        return $this->belongsTo(User::class, 'last_reply_user_id');
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

    public function inVisible()
    {
        return $this->status < 0 && !(\Auth::id() == $this->user->id || \Auth::user()->can('view_thread'));
    }

    public function scopeVisible($query)
    {
        return $query->where('status', '>=', static::VISIBLE);
    }

    public function scopeAudit($query)
    {
        return $query->where('status', static::Audit);//审核中
    }

    public function scopeTrash($query)
    {
        return $query->where('status', static::TRASH);//回收站
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
        return $query->orderBy('order', 'desc')->orderBy('heat', 'desc');
    }

    public function scopePinAndRecentReply($query)
    {
        return $query->orderBy('order', 'desc')->orderBy('updated_at', 'desc');
    }

    public function scopeExcellent($query)
    {
        return $query->where('is_excellent', '=', true);
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

    /**
     * Get the presenter class.
     *
     * @return string
     */
//    public function getPresenterClass()
//    {
//        return ThreadPresenter::class;
//    }

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
                $query->where('body', 'LIKE', "%$value%");
            } elseif ($key == 'title') {
                $query->where('title', 'LIKE', "%$value%");
            } elseif ($key == 'date_start') {
                $query->where('created_at', '>=', $value);
            } elseif ($key == 'date_end') {
                $query->where('created_at', '<=', $value);
            } elseif ($key == 'orderType'){
                $query->orderBy($value,'desc');
            } else {
                $query->where($key, $value);
            }
        }
    }

    public function getExcerptAttribute($value)
    {
        return $value ?: '点击查看更多';
    }

    public function getThumbnailsAttribute($value)
    {
        return $value ?: request()->getSchemeAndHttpHost().'/images/share.png';
    }

    //动态计算热度值
    public function getHeatAttribute()
    {
        $view_score = 1;
        $like_score = 20;
        $reply_score = 10;
        $time_score = 1000;
        $excellent = $this->is_excellent != 0 ? 500 : 0;

        $createAt = new Carbon($this['attributes']['created_at']);
        $now = Carbon::now();
        $timeAlive = $now->diffInSeconds($createAt);
        $heat = $this->view_count * $view_score + $this->like_count * $like_score + $this->reply_count * $reply_score + $excellent
            + $this->heatCoolingValue($timeAlive, $time_score) + $this->heat_offset;
        return round($heat, 2, PHP_ROUND_HALF_DOWN);
    }

    private function heatCoolingValue($timeAlive, $time_score)
    {
        //72小时后逐渐降低，72小时为中点
        if ($timeAlive <= 72 * 2 * 60 * 60) {
            return $time_score * cos($timeAlive * PI() / (60 * 60 * 72 * 2)) + 100;
        } else {
            return ($score = $timeAlive * (-1) + 60 * 60 * 72 * 2 - 900) > -100000 ? $score : -100000;
        }
    }
}
