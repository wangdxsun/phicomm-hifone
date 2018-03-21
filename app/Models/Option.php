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

class Option extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'thread_id',
        'order',
        'content',
        'vote_count',
    ];

    public $validationMessages = [
        'content.required' => '选项内容是必填字段',
        'content.min' => '选项内容最少1个字符',
        'content.max' => '选项内容最多40个字符',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'content'      => 'required|max:40|min:1',
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class)->orderBy('order');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'option_user')
            ->withTimestamps()->withPivot('option_id', 'user_id')->orderBy('option_user.created_at', 'desc');
    }

}
