<?php

namespace Hifone\Models;

class Notice extends BaseModel
{
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
        'user_id',
        'title',
        'content',
        'type',
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'title'        => 'required|min:2',
        'content'      => 'required|min:2'
    ];

}
