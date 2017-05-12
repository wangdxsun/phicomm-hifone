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

class Log extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'logable_id',
        'logable_type',
        'type',
        'body',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'user_id'   => 'required|int',
    ];

    public function logable()
    {
        return $this->morphTo();
    }
}
