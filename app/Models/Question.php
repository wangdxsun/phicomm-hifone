<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:45
 */

namespace Hifone\Models;

use AltThree\Validator\ValidatingTrait;
use Elasticquent\ElasticquentTrait;
use Hifone\Models\Scopes\CommonTrait;
use Hifone\Models\Traits\Taggable;
use Hifone\Services\Tag\TaggableInterface;

class Question extends BaseModel implements TaggableInterface
{
    use CommonTrait, Taggable, ValidatingTrait, Taggable, ElasticquentTrait;

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

    protected $hidden = [
        'user_id',
        'body_original',
        'bad_word',
        'device',
        'ip',
        'last_op_user_id',
        'last_op_time',
        'last_op_reason',
        'last_answer_time',
        'deleted_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->select(['id','username', 'password', 'score']);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function scopeOfTag($query, $tagId)
    {
        return $query->whereHas('tags', function ($query) use ($tagId) {
            return $query->where('tag_id', $tagId);
        });
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

    //审核通过和通过后删除
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
