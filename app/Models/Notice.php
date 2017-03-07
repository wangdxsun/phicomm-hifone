<?php

namespace Hifone\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
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
}
