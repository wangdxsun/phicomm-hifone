<?php

namespace Hifone\Models;

use Illuminate\Database\Eloquent\Model;
use AltThree\Validator\ValidatingTrait;


class Word extends Model
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
        'word',
        'type',
        'action',
        'substitute',
        'created_at',
        'updated_at'
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'word'        => 'required|min:2',
        'action'      => 'required|min:2'
    ];

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return;
        }

        return  $query->where(function ($query) use ($search) {
            $query->where('word', 'LIKE', "%$search%");
        });
    }



}
