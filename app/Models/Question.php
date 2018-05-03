<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:45
 */

namespace Hifone\Models;

use Hifone\Models\Scopes\CommonTrait;
use Hifone\Models\Traits\Taggable;

class Question extends BaseModel
{
    use CommonTrait, Taggable;

    //问题状态
    const VISIBLE = 0;//正常问题
    const TRASH = -1;//审核未通过
    const AUDIT = -2;//审核中
    const DELETED = -3;//已删除

    public $fillable = [
        'title',
        'body',
        'status',
        'score',
        'user_id',
        'order',
        'device',
        'ip',
        'created_at',
        'updated_at',
        'thumbnails'
    ];

    protected $hidden = [];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

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

    public function getUrlAttribute()
    {
        return ;
    }

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    //置顶图标
    public function getPinAttribute()
    {
        return $this->order == 1 ? 'fa fa-thumb-tack text-danger' : 'fa fa-thumb-tack';
    }

    //下沉图标
    public function getSinkAttribute()
    {
        return $this->order < 0 ? 'fa fa-anchor text-danger' : 'fa fa-anchor';
    }

    //精华图标
    public function getExcellentAttribute()
    {
        return $this->is_excellent ? 'fa fa-diamond text-danger' : 'fa fa-diamond';
    }
}
