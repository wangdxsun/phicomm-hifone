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
use Hifone\Models\Scopes\ForUser;
use Hifone\Models\Scopes\Recent;
use Hifone\Models\Traits\Taggable;
use Hifone\Presenters\ThreadPresenter;
use Hifone\Services\Tag\TaggableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Input;
use McCool\LaravelAutoPresenter\HasPresenter;
use Venturecraft\Revisionable\RevisionableTrait;

class Thread extends Model implements HasPresenter, TaggableInterface
{
    use ValidatingTrait, Taggable, ForUser, Recent, RevisionableTrait, SoftDeletes;

    const TRASH = -1;//回收站
    const Audit = -1;//待审核

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
        'title'   => 'required|min:2',
        'body'    => 'required|min:2',
        'node_id' => 'required|int',
        'user_id' => 'required|int',
    ];

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function follows()
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'object');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function node()
    {
        return $this->belongsTo(Node::class);
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
        $this->updated_at = $lastReply->created_at;
        $this->save();
    }

    public function inVisible()
    {
        return $this->status < 0 && (!\Auth::check() || !\Auth::user()->can('view_thread'));
    }

    public function scopeVisible($query)
    {
        return $query->where('status', '>=', 0);
    }

    public function scopeAudit($query)
    {
        return $query->where('status', -2);//审核中
    }

    public function scopeTrash($query)
    {
        return $query->where('status', -1);//回收站
    }

    public function scopeTitle($query, $search)
    {
        if (!$search) {
            return null;
        }

        return $query->where('title', 'LIKE', "%$search%");
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
        return $query->whereRaw("(`created_at` > '" . Carbon::today()->subMonths(6)->toDateString() . "' or (`order` > 0) )")
            ->visible()
            ->orderBy('order', 'desc')
            ->orderBy('updated_at', 'desc');
    }

    public function scopeRecentReply($query)
    {
        return $query->visible()
            ->orderBy('order', 'desc')
            ->orderBy('updated_at', 'desc');
    }

    public function scopeExcellent($query)
    {
        return $query->where('is_excellent', '=', true)->visible();
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
    public function getPresenterClass()
    {
        return ThreadPresenter::class;
    }

    public function getReportAttribute()
    {
        return $this->title;
    }

    public function getUrlAttribute()
    {
        return route('thread.show', $this->id);
    }
}
