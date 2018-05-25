<?php
/**
 * Created by PhpStorm.
 * User: qiuling.jiang
 * Date: 2018/5/3
 * Time: 20:24
 */

namespace Hifone\Models;

class Invite extends BaseModel
{
    protected $guarded = ['id'];

    public $rules = [
        'from_user_id'        => 'required|int',
        'to_user_id'    => 'required|int',
        'question_id'  => 'required|int',
    ];

    public function from()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function to()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function scopeQuestion($query, Question $question)
    {
        return $query->where('question_id', $question->id);
    }
}