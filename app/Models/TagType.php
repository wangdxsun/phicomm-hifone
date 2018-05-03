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


    const THREAD = 0;//type:帖子标签
    const USER = 1;  //type:用户标签
    const QUESTION = 2;  //type:问题标签


    protected $fillable = [
        'display_name',
        'created_at',
        'updated_at',
        'type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'type'
    ];

     public static $types  = [
        [
            'value' => 0,
            'name' => 'thread',
            'display_name' => '帖子标签'],
        [
            'value' => 1,
            'name' => 'user',
            'display_name' => '用户标签'],
        [
            'value' => 2,
            'name'  => 'question',
            'display_name'  => '问题标签'],

    ];

    //type字段表征标签类型
    public function tags()
    {
        return $this->hasMany(Tag::class,'tag_type_id','id');
    }

    public function scopeOfType($query, $id)
    {
        return $query->where('type', $id);
    }

}