<?php

namespace Hifone\Models;

class Word extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'admin',
        'type',
        'find',
        'replacement',
        'substitute',
        'extra',
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
        'find'       => 'required|min:1',
        'replacement'=> 'required|min:1'
    ];

}
