<?php

namespace Hifone\Models;

use AltThree\Validator\ValidatingTrait;

class Keyword extends BaseModel
{
    public $timestamps = true;

    protected $dates = [];

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'word',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'word'       => 'required',
    ];

}