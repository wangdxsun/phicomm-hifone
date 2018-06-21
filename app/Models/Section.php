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

use Venturecraft\Revisionable\RevisionableTrait;

class Section extends BaseModel
{
    use RevisionableTrait;

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
    public $validationMessages = [
        'name.required' => '分类名称是必填字段',
        'name.min' => '分类名称最少2个字符',
        'name.max' => '分类名称最多50个字符',
        'description.required' => '分类描述是必填字段',
        'description.min' => '分类描述最少2个字符',
        'description.max' => '分类描述最多50个字符',
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
