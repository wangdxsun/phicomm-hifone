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

use Hifone\Presenters\NodePresenter;

class Chat extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'from',
        'to',
        'from_to',
        'message',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'from'      => 'required|int',
        'to'     => 'required|int',
        'from_to'    => 'required|int',
        'message' => 'required|string',
    ];

    public function from()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function to()
    {
        return $this->belongsTo(User::class, 'to');
    }

    public function scopeChatWith($query, User $user)
    {
        return $query->where('from_to', \Auth::id() * $user->id);
    }

    public function scopeMy($query)
    {
        return $query->where('from', \Auth::id())->orWhere('to', \Auth::id());
    }
}
