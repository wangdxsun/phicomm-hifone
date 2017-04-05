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

use AltThree\Validator\ValidatingTrait;
use Illuminate\Database\Eloquent\Model;

class CreditRule extends Model
{
    use ValidatingTrait;

    const NO_LIMIT = 0;

    const DAILY = 1;

    const ONCE = 2;

    public $types = [
        self::NO_LIMIT => '无限制',
        self::DAILY => '每天',
        self::ONCE => '一次',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'credit_rules';

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'slug', 'reward', 'type', 'times'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'name'      => 'required|string',
        'slug'      => 'required|string',
        'reward'    => 'required|int',
        'type'      => 'required|int',
        'times'     => 'required|int',
    ];

    public function getTypeStrAttribute()
    {
        return $this->types[$this->type];
    }

    public function getTimesAttribute($value)
    {
        return $this->type == static::DAILY ? $value : '';
    }

    public function setTimesAttribute($value)
    {
        $this->attributes['times'] = ($this->type == static::DAILY ? $value : 0);
    }
}
