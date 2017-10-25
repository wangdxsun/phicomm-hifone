<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Models;

class Chat extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'from_to',
        'message',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'from_user_id'      => 'required|int',
        'to_user_id'     => 'required|int',
        'from_to'    => 'required|int',
        'message' => 'required|string',
    ];

    public function from()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function to()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function scopeChatWith($query, User $user)
    {
        return $query->where('from_to', \Auth::id() * $user->id);
    }

    public function scopeMy($query)
    {
        return $query->where('from_user_id', \Auth::id())->orWhere('to_user_id', \Auth::id());
    }

    public function scopeSearch($query, $searches = [])
    {
        foreach ($searches as $key => $value) {
            if ($key == 'from_user_id') {
                $query->whereHas('from', function ($query) use ($value){
                    $query->where('username', 'like',"%$value%");
                });
            } elseif ($key == 'to_user_id') {
                $query->whereHas('to', function ($query) use ($value){
                    $query->where('username', 'like',"%$value%");
                });
            } elseif ($key == 'message') {
                $query->where('message', 'LIKE', "%$value%");
            } elseif ($key == 'date_start') {
                $query->where('created_at', '>=', $value);
            } elseif ($key == 'date_end') {
                $query->where('created_at', '<=', $value);
            } else {
                $query->where($key, $value);
            }
        }
    }
}
