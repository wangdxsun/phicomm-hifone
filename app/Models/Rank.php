<?php
namespace Hifone\Models;

class Rank extends BaseModel
{
    protected $table = 'ranks';

    public $fillable = [
        'reply_count',
        'like_count',
        'favorite_count',
        'user_id',
        'start_date',
        'end_date'
    ];

}