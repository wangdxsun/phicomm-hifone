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
        'last_op_user_id',
        'type',
        'word',
        'status',
        'replacement',
        'created_at',
        /*'updated_at'*/
        'last_op_time',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'type'       => 'required',
        'word'       => 'required|min:1',
        'status'     => 'required|min:1'
    ];

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

}
