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

class Link extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'title',
        'url',
        'description',
        'cover',
        'order',
        'created_at',
        'updated_at',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'title'   => 'string|required',
        'url'     => 'string|required',
        'status'  => 'int',
    ];
}
