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
    protected $fillable = [
        'display_name',
        'created_at',
        'updated_at'
    ];

}