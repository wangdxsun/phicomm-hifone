<?php
/**
 * Created by PhpStorm.
 * User: meng.dai
 * Date: 2018/3/1
 * Time: 10:13
 */

namespace Hifone\Models;

class TagType extends BaseModel
{
    public $table = 'tag_types';
    const THREAD = 0;
    const USER = 1;
    const AUTO = 2;
    protected $fillable = [
        'display_name',
        'created_at',
        'updated_at',
        'type'
    ];

    public static $tagTypeTypes = [
        [
            'value' => 0,
            'name' => 'thread',
            'display_name' => '帖子标签'],
        [
            'value' => 1,
            'name' => 'user',
            'display_name' => '用户标签'],
    ];

    public function tags()
    {
        return $this->hasMany(Tag::class,'type','id');
    }

    public function scopeOfType($query, $id)
    {
        return $query->whereIn('type', $id);
    }

}