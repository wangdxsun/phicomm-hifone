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
        'end_date',
        'week_rank',
        'followed'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

}