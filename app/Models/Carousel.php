<?php

namespace Hifone\Models;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
    protected $fillable = [
        'image',
        'url',
        'order',
        'description',
        'created_at',
        'updated_at',
    ];

    public $rules = [
        'image'   => 'string|required',
        'order'  => 'int',
        'url' => 'string|required',
        'description' => 'string|required'
    ];
}
