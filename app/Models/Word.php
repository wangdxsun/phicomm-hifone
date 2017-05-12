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
