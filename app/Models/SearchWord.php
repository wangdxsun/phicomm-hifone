<?php
namespace Hifone\Models;

class SearchWord extends BaseModel
{
    protected $table = 'search_words';
    protected $fillable = [
        'word',
        'count',
        'stat_count',
        'created_at',
        'updated_at'
    ];

    public $rules = [
    ];
}