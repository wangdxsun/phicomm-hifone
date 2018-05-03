<?php
namespace Hifone\Models;

class Answer extends BaseModel
{
    protected $table = 'answers';

    public $fillable = [
        'body',
        'user_id',
        'question_id',
        'device',
        'status',
        'ip',
        'thumbnails'
    ];

    public $rules = [
        'body'         => 'required|min:5|max:800',
        'user_id'      => 'required|int',
        'question_id'  => 'required|int',
    ];

    //审核通过
    public function scopeVisible($query)
    {
        return $query->where('status', Question::VISIBLE);
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

}