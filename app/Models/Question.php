<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 9:45
 */

namespace Hifone\Models;

use Carbon\Carbon;
use Elasticquent\ElasticquentTrait;
use Hifone\Models\Scopes\CommonTrait;
use Hifone\Models\Traits\Taggable;
use Hifone\Services\Tag\TaggableInterface;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Question extends BaseModel implements TaggableInterface
{
    use CommonTrait, Taggable, ElasticquentTrait, SoftDeletes;

    //问题状态
    const VISIBLE = 0;//正常问题
    const TRASH = -1;//审核未通过
    const AUDIT = -2;//审核中
    const DELETED = -3;//已删除

    protected $guarded = ['id'];

    protected $hidden = [
        'body_original',
        'bad_word',
        'device',
        'ip',
        'last_op_user_id',
        'last_op_time',
        'last_op_reason',
        'deleted_at',
        'updated_at',
        'like_count'
    ];

    protected $mappingProperties = [
        'title' => [
            'type' => 'string',
            'analyzer' => 'ik_max_word',
        ],
        'body' => [
            'type' => 'string',
            'analyzer' => 'ik_max_word',
        ],
        'created_at' => [
            'type' => 'date',
            'format' => 'yyyy-MM-dd HH:mm',
        ]
    ];

    protected $dates = ['deleted_at', 'first_answer_time'];

    public function getDates()
    {
        return $this->dates;
    }

    //消息推送用phicomm_id
    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'username', 'avatar_url', 'role', 'score', 'phicomm_id']);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function follows()
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    public function followedUsers()
    {
        return $this->morphToMany(User::class, 'followable', 'follows');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'object');
    }

    //关注问题的记录AddFollowCommandHandler
    public function followers()
    {
        return $this->morphMany(Follow::class, 'followable');
    }

    //问题详情是否可见
    public function isVisible()
    {
        //按照问题状态、用户登录态、用户是否问题提问者、分端编排
        if ($this->status == Question::VISIBLE) {
            return true;
        } elseif ($this->status == Question::TRASH) {
            if (Auth::guest()) {
                return false;
            } else {
                return Auth::id() == $this->user->id;
            }
        } elseif ($this->status == Question::AUDIT || $this->status == Question::DELETED) {
            if (Auth::guest()) {
                return false;
            } else {
                if (isWeb()) {
                    return Auth::id() == $this->user->id || Auth::user()->can('view_thread');
                } else {
                    return Auth::id() == $this->user->id;
                }
            }
        }
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

    public function scopeLastAnswer($query)
    {
        return $query->orderBy('last_answer_time', 'desc');
    }

    public function getUrlAttribute()
    {
        return route('question.show', $this->id);
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
                $query->where('title', 'LIKE', "%$value%");
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
            } elseif ($key == 'tag') {
                $query->whereHas('tags', function ($query) use ($value) {
                   $query->where('tag_id', $value);
                });
            } else {
                $query->where($key, $value);
            }
        }
    }
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function getReportAttribute()
    {
        return $this->title;
    }

    //需要提醒采纳的,还剩48h到期~12h，防止服务因异常中断
    public function scopeRemindAdopt($query)
    {
        return $query->where('answer_id', null)->whereBetween('first_answer_time', [Carbon::now()->subHours(5*24 - 12), Carbon::now()->subHours(5*24 - 48)]);
    }

    //尚未采纳且需要自动采纳的,现在时间介于首答时间15天后和再多一个小时之内
    public function scopeAutoAdopted($query)
    {
        return $query->where('answer_id', null)->whereBetween('first_answer_time', [Carbon::now()->subHours(15*24 + 1), Carbon::now()->subHours(15*24)]);
    }

}
