<?php

namespace Hifone\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes;

    const DELETE = 1;//已删除
    const IGNORE = 2;//已忽略

    protected $dates = ['deleted_at', 'last_op_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportable()
    {
        return $this->morphTo();
    }

    public function scopeAudit($query)
    {
        return $query->where('status', 0);
    }

    public function scopeAudited($query)
    {
        return $query->where('status', '>', 0);
    }

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    public function getOpResultAttribute()
    {
        switch ($this->status) {
            case 0:
                return '待处理';
            case 1:
                return '已删除';
            case 2:
                return '已忽略';
            default:
                return '未知';
        }
    }

    public function getTypeAttribute()
    {
        switch ($this->reportable_type) {
            case Thread::class:
                return '举报帖子';
            case Reply::class:
                return '举报回帖';
            default:
                return '未知';
        }
    }
}
