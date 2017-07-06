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

    const VISIBLE = 0;//正常帖子
    const TRASH = -1;//回收站
    const Audit = -2;//待审核

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'body',
        'user_id',
        'thread_id',
        'body_original',
    ];

    protected $dates = ['deleted_at', 'last_op_time'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'thread_id' => 'required|int',
        'body'      => 'required|max:1024',
        'user_id'   => 'int',
    ];
    public static $orderTypes = [
        'id' => '回复ID',
        'user_id'  => '回帖人',
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
        return $this->belongsTo(User::class);
    }

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
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
        return $query->where('status', -1)->orWhere('status', -5);//回收站
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

    public function scopeSearch($query,$searches = [])
    {
        foreach ($searches as $key => $value) {
            if ($key == 'thread_title') {
                $query->whereHas('thread', function ($query) use ($value){
                    $query->where('title', 'like', "%$value%");
                });
            } elseif ($key == 'username') {
                $query->whereHas('user', function ($query) use ($value){
                    $query->where('username', $value);
                });
            } elseif ($key == 'body') {
                $query->where('body', 'LIKE', "%$value%");
            } else if ($key == 'date_start') {
                $query->where('created_at', '>=', $value);
            } else if ($key == 'date_end') {
                $query->where('created_at', '<=', $value);
            } elseif ($key == 'orderType'){
                $query->orderBy($value);
            }
        }
    }
}
