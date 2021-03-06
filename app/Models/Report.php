<?php

namespace Hifone\Models;

use Hifone\Models\Scopes\ForUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends BaseModel
{
    use SoftDeletes, ForUser;

    const DELETE = 1;//已删除
    const IGNORE = 2;//已忽略

    public static $reason = [
        '恶意灌水',
        '恶意攻击谩骂',
        '营销广告',
        '淫晦色情',
        '政治反动',
        '其他原因',
    ];

    protected $dates = ['deleted_at', 'last_op_time'];

    protected $fillable = ['user_id', 'reportable_id', 'reportable_type', 'reason'];

    public $rules = [
        'user_id' => 'required|int',
        'reportable_id' => 'required|int',
        'reportable_type' => 'required|in:Hifone\Models\Thread,Hifone\Models\Reply,Hifone\Models\Question,Hifone\Models\Answer,Hifone\Models\Comment',
        'reason' => 'required|string',
    ];

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

    public function scopeOfType($query, $type)
    {
        return $query->where('reportable_type', $type);
    }

    public function scopeOfId($query, $id)
    {
        return $query->where('reportable_id', $id);
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
            case Question::class:
                return '举报提问';
            case Answer::class:
                return '举报回答';
            case Comment::class:
                return '举报回复';
            default:
                return '未知';
        }
    }

    public function scopeThreadAndReply($query)
    {
        return $query->whereIn('reportable_type', ['Hifone\Models\Thread', 'Hifone\Models\Reply']);
    }

    public function scopeQuestionAndAnswer($query)
    {
        return $query->whereIn('reportable_type', ['Hifone\Models\Question', 'Hifone\Models\Answer', 'Hifone\Models\Comment']);
    }
}
