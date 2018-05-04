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

    const VISIBLE = 0;//正常问题
    const TRASH = -1;//审核未通过
    const AUDIT = -2;//审核中
    const DELETED = -3;//已删除

    public $fillable = [
        'body',
        'user_id',
        'answer_id',
        'comment_id',
        'device',
        'status',
        'ip',
    ];

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

    public function User()
    {
        return $this->belongsTo(User::class);
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

}