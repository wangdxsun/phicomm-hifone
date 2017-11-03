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

use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends BaseModel
{
    use SoftDeletes;

    const VISIBLE = 0;//正常回复
    const TRASH = -1;//审核未通过
    const AUDIT = -2;//待审核 or 审核未通过
    const DELETED = -3;//已删除

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'body',
        'user_id',
        'thread_id',
        'reply_id',
        'body_original',
        'last_op_reason',
        'ip',
    ];

    protected $hidden = ['body_original', 'bad_word', 'is_block', 'ip', 'last_op_user_id', 'last_op_time', 'last_op_reason',
        'updated_at', 'deleted_at'];

    protected $dates = ['deleted_at', 'last_op_time'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'thread_id' => 'required|int',
        'reply_id' => 'int',
        'body'      => 'required|max:15000',
        'user_id'   => 'int',
    ];
    public static $orderTypes = [
        'id' => '回复时间',
        'user_id'  => '回帖人',
    ];

    public static $orderByThreadId = [
        'thread_id' => '发帖时间',
    ];

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'object');
    }

    public function user()
    {
        return $this->belongsTo(User::class)
            ->select(['id', 'username', 'avatar_url','password','score',
            'notification_reply_count','notification_at_count',
                'notification_system_count','notification_chat_count','notification_follow_count']);
    }

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    //评论的所有回复
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    //回复所属的评论或回复
    public function reply()
    {
        return $this->belongsTo(Reply::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', '>=', Reply::VISIBLE);
    }

    public function scopeAudit($query)
    {
        return $query->where('status', Reply::AUDIT);//审核中
    }

    public function scopeTrash($query)
    {
        return $query->whereIn('status', [static::TRASH, static::DELETED])->orWhere('status', -5);//回收站
    }

    //正常和已删除
    public function scopeVisibleAndDeleted($query)
    {
        return $query->whereIn('status', [static::VISIBLE, static::DELETED]);
    }

    public function scopePinAndRecent($query)
    {
        return $query->orderBy('order', 'desc')->orderBy('created_at', 'desc');
    }

    //评论可见性
    public function getVisibleAttribute()
    {
        return $this->status == static::VISIBLE || $this->status == static::DELETED;
    }

    public function getPinAttribute()
    {
        return $this->order > 0 ? 'fa fa-thumb-tack text-danger' : 'fa fa-thumb-tack';
    }

    public function getReportAttribute()
    {
        return $this->body;
    }

    public function getUrlAttribute()
    {
        return $this->thread->url . '#reply' . $this->id;
    }

    public function getHighlightAttribute()
    {
        return $this->like_count > 0 ? 'highlight' : null;
    }

//    public function getBodyAttribute($value)
//    {
//        return $this->status < Reply::VISIBLE ? "该评论已删除" : $value;
//    }

    public function scopeSearch($query,$searches = [])
    {
        foreach ($searches as $key => $value) {
            if ($key == 'thread_title') {
                $query->whereHas('thread', function ($query) use ($value){
                    $query->where('title', 'like', "%$value%");
                });
            } elseif ($key == 'username') {
                $query->whereHas('user', function ($query) use ($value){
                    $query->where('username','like',"%$value%");
                });
            } elseif ($key == 'body') {
                $query->where('body', 'LIKE', "%$value%");
            } elseif ($key == 'date_start' && null != $value ) {
                $query->where('created_at', '>=', $value);
            } elseif ($key == 'date_end' && null != $value ) {
                $query->where('created_at', '<=', $value);
            } elseif ($key == 'orderByThreadId'){
                    $query->orderBy($value,'desc');
            } elseif ($key == 'orderType'){
                $query->orderBy($value,'desc');
            }
        }
    }

}
