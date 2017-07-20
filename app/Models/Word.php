<?php

namespace Hifone\Models;

use AltThree\Validator\ValidatingTrait;

class Word extends BaseModel
{
    use ValidatingTrait;
    // manually maintain
    public $timestamps = false;

    //use SoftDeletingTrait;
    protected $dates = ['deleted_at'];

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'last_op_user_id',
        'type',
        'word',
        'replacement',
        'status',
        'created_at',
        /*'updated_at'*/
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'type'       => 'required',
        'word'       => 'required|min:1',
        'status'=> 'required|min:1'
    ];

    public static $statuses = [
        '替换关键词',
        '审核关键词',
        '禁止关键词'
    ];

    public static $types = [
        '政治',
        '广告',
        '涉枪涉爆',
        '网络招嫖',
        '淫秽信息',
        '默认',
    ];

    public function scopeSearch($query, $searches = [])
    {
        foreach ($searches as $key => $value) {
            if ($key == 'word') {
                $query->where('word', 'LIKE', "%$value%");
            } else {
                $query->where($key, $value);
            }
        }
    }
}
