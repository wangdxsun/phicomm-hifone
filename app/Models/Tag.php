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

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name', 'type', 'count',
    ];
    const AUTO = 0;
    const HUMAN = 1;

    /**
     * Tags can have many threads.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany 
     */
    public function threads()
    {
        return $this->morphedByMany(Thread::class, 'taggable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class,'taggable');
    }

    //根据标签类别查询标签
    public function scopeOfType($query, TagType $tagType)
    {
        return $query->where('type', $tagType->id);
    }

    //查询标签所属类别
    public function tagType()
    {
        return $this->belongsTo(TagType::class,'type','id');
    }

    //查询是自动标签还是手动标签
    public function scopeOfChannel($query,$channel)
    {
        return $query->where('channel', $channel);
    }


}
