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

use Hifone\Presenters\NodePresenter;
use McCool\LaravelAutoPresenter\HasPresenter;

class Node extends BaseModel implements HasPresenter
{
    /**
     * List of attributes that have default values.
     *
     * @var mixed[]
     */

    protected $attributes = [
        'section_id' => 0,
    ];
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'section_id',
        'name',
        'slug',
        'order',
        'icon',
        'icon_list',
        'icon_detail',
        'android_icon',
        'android_icon_list',
        'android_icon_detail',
        'ios_icon',
        'ios_icon_list',
        'ios_icon_detail',
        'web_icon_detail',
        'web_icon_list',
        'description',
        'thread_count',
        'reply_count',
        'status',
        'created_at',
        'updated_at',
        'is_prompt',
        'prompt',
        'is_show',
        'is_feedback',
        'feedback_thread_id'
    ];

    protected $hidden = [
        'status',
        'last_op_user_id',
        'last_op_time',
        'created_at',
        'updated_at',
        'order',
        'slug',
        'section_id',
        'reply_count',
        'ios_icon',
        'android_icon',
        'ios_icon_detail',
        'ios_icon_list',
        'android_icon_detail',
        'android_icon_list',
        'web_icon_detail',
        'web_icon_list'
    ];

    /**
     * The validation rules
     * @var string[]
     */
    public $rules = [
        'name'                => 'required|string|min:2|max:50',
        'order'               => 'int',
        'status'              => 'int',
        'description'         => 'required|string|min:2|max:100',
        'prompt'              => 'string|min:10|max:40',
    ];

    public $validationMessages = [
        'name.required'               => '主版块名称是必填字段',
        'name.min'                    => '主版块名称最少2个字符',
        'name.max'                    => '主版块名称最多50个字符',
        'prompt.required'             => '提示语是必填字段',
        'prompt.min'                  => '提示语最少10个字符',
        'prompt.max'                  => '提示语最多40个字符',
        'description.required'        => '主版块描述是必填字段',
        'description.min'             => '主版块描述最少2个字符',
        'description.max'             => '主版块描述最多100个字符',
    ];

    /**
     * Nodes can belong to a section.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo(Section::class)->select(['id', 'name']);
    }

    /**
     * Lookup all of the threads posted on the node.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Returns url of this node.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return route('node.show', $this->id);
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return NodePresenter::class;
    }

    /**
     * define morph relations
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function dailyStats()
    {
        return $this->morphMany(DailyStat::class,'object');
    }

    public function subNodes()
    {
        return $this->hasMany(SubNode::class)->orderBy('order');
    }

    public function scopeShow($query)
    {
        return $query->where('is_show', 1);
    }

    public function replies()
    {
        return $this->hasManyThrough(Reply::class,Thread::class);
    }
    
    //查询主板块下版主信息
    public function moderators()
    {
        return $this->belongsToMany(User::class,'moderators','node_id','user_id');
    }

    //查询主板块下实习版主信息
    public function praModerators()
    {
        return $this->belongsToMany(User::class, 'pra_moderators', 'node_id','user_id');
    }

    public function getIconAttribute()
    {
        $userAgent = get_request_agent();
        if ($userAgent == Thread::IOS) {
            return $this->attributes['ios_icon'];
        } elseif ($userAgent == Thread::ANDROID) {
            return $this->attributes['android_icon'];
        } elseif ($userAgent == Thread::H5) {
            return $this->attributes['icon'];
        } else {
            return '';
        }
    }

    public function getIconDetailAttribute()
    {
        $userAgent = get_request_agent();
        if ($userAgent == Thread::IOS) {
            return $this->attributes['ios_icon_detail'];
        } elseif ($userAgent == Thread::ANDROID) {
            return $this->attributes['android_icon_detail'];
        } elseif ($userAgent == Thread::H5) {
            return $this->attributes['icon_detail'];
        } else {
            return $this->attributes['web_icon_detail'];
        }
    }

    public function getIconListAttribute()
    {
        $userAgent = get_request_agent();
        if ($userAgent == Thread::IOS) {
            return $this->attributes['ios_icon_list'];
        } elseif ($userAgent == Thread::ANDROID) {
            return $this->attributes['android_icon_list'];
        } elseif ($userAgent == Thread::H5) {
            return $this->attributes['icon_list'];
        } else {
            return $this->attributes['web_icon_list'];
        }
    }

    public function getH5IconAttribute()
    {
        return $this->attributes['icon'];
    }

    public function getH5IconDetailAttribute()
    {
        return $this->attributes['icon_detail'];
    }

    public function getH5IconListAttribute()
    {
        return $this->attributes['icon_list'];
    }

    //是否在意见反馈显示该主版块
    public function scopeFeedback($query)
    {
        return $query->whereNotNull('feedback_thread_id');
    }

    //版块的关注信息
    public function followers()
    {
        return $this->morphMany(Follow::class,'followable');
    }

    //关注板块的用户
    public function users()
    {
        return $this->morphToMany(User::class, 'followable', 'follows');
    }

}
