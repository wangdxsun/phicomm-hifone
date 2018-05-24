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
    protected $fillable = ['name', 'tag_type_id', 'count', 'order'];

    protected $hidden = ['created_at', 'updated_at', 'tag_type_id', 'channel', 'count', 'pivot', 'order'];

    const AUTO = 0;

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
        return $this->morphedByMany(User::class, 'taggable');
    }

    //根据标签类别查询标签
    public function scopeOfTagType($query, TagType $tagType)
    {
        return $query->where('tag_type_id', $tagType->id);
    }

    //查询标签所属类别
    public function tagType()
    {
        return $this->belongsTo(TagType::class,'tag_type_id','id');
    }

    //查询是自动标签还是手动标签
    public function scopeOfChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeOfNotAuto($query)
    {
        return $query->where('channel', '<>',0);
    }

    public static function findTagByName($name)
    {
        return static::where('name', $name)->first();
    }

}
