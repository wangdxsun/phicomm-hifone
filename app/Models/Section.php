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
use Venturecraft\Revisionable\RevisionableTrait;

class Section extends BaseModel
{
    use ValidatingTrait, RevisionableTrait;

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'order',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $hidden = ['created_at', 'updated_at', 'order', 'description'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'name'      => 'required|string',
        'order'     => 'int',
    ];

    /**
     * Sections can have many nodes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function nodes()
    {
        return $this->hasMany(Node::class)->orderBy('order');
    }
}
