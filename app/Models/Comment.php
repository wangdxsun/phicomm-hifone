<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 10:02
 */

namespace Hifone\Models;

use Hifone\Models\Scopes\CommonTrait;

class Comment extends BaseModel
{
    use CommonTrait;

    const VISIBLE = 0;//审核通过回复
    const TRASH = -1;//审核未通过
    const AUDIT = -2;//审核中
    const DELETED = -3;//已删除

    protected $guarded = ['id'];

    protected $hidden = [
        'body_original',
        'bad_word',
        'user_id',
        'answer_id',
        'comment_id',
        'device',
        'ip',
        'last_op_user_id',
        'last_op_time',
        'last_op_reason',
        'updated_at',
        'deleted_at'
    ];

    //消息推送用phicomm_id
    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'username', 'avatar_url', 'role', 'score', 'phicomm_id']);
    }

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    //审核通过
    public function scopeVisible($query)
    {
        return $query->where('status', static::VISIBLE);
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

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'object');
    }

    //置顶图标
    public function getPinAttribute()
    {
        return $this->order == 1 ? 'fa fa-thumb-tack text-danger' : 'fa fa-thumb-tack';
    }

    public function scopeSearch($query, $searches = [])
    {
        foreach ($searches as $key => $value) {
            if ($key == 'user_name') {
                $query->whereHas('user', function ($query) use ($value){
                    $query->where('username', 'like',"%$value%");
                });
            } elseif ($key == 'body') {
                $query->where('body', 'LIKE', "%$value%");
            } elseif ($key == 'title') {
                $query->whereHas('answer.question', function ($query) use ($value){
                    $query->where('title', 'LIKE', "%$value%");
                });
            } elseif ($key == 'tag'){
                $query->whereHas('answer.question.tags', function ($query) use ($value){
                    $query->where('tag_id', $value);
                });

            } elseif ($key == 'date_start') {
                if ($value == "") {
                    continue;
                }
                $query->where('created_at', '>=', $value);
            } elseif ($key == 'date_end') {
                if ($value == "") {
                    continue;
                }
                $query->where('created_at', '<=', $value);
            } else {
                $query->where($key, $value);
            }
        }
    }

}