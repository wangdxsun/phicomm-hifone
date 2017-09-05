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
        'description',
        'thread_count',
        'reply_count',
        'status',
        'created_at',
        'updated_at',
        'is_prompt',
        'prompt',
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
        'reply_count'
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'name'        => 'required|string|min:2|max:50',
        'order'       => 'int',
        'status'      => 'int',
        'description' => 'required|string|min:2|max:100',
        'prompt'      => 'string|min:10|max:40',
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
        return ($this->slug) ? route('go', $this->slug) : route('node.show', $this->id);
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

    public function moderators()
    {
        return $this->hasMany(Moderator::class);
    }
}
