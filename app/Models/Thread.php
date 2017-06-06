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
        'is_excellent',
        'created_at',
        'updated_at',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'title'   => 'required|min:2|max:80',
        'body'    => 'required|min:2',
        'node_id' => 'required|int',
        'user_id' => 'required|int',
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

    public function scopeOfNode($query, Node $node)
    {
        return $query->where('node_id', $node->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
        $lastReply = $this->replies()->recent()->first();

        $this->last_reply_user_id = $lastReply ? $lastReply->user_id : 0;
        $this->updated_at = $lastReply->created_time;
        $this->save();
    }

    public function inVisible()
    {
        return $this->status < 0 && (!\Auth::check() || !\Auth::user()->can('view_thread'));
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

    public function scopePinAndRecentReply($query)
    {
        $days = Config::get('settings.hot_thread', 14);
        return $query->whereRaw("(`created_at` > '" . Carbon::today()->subDays($days)->toDateString() . "' or (`order` > 0) )")
            ->orderBy('order', 'desc')
            ->orderBy('updated_at', 'desc');
    }

    public function scopeRecentReply($query)
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
}
