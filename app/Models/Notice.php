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
use Carbon\Carbon;
use Config;
use Hifone\Models\Scopes\ForUser;
use Hifone\Models\Scopes\Recent;
use Hifone\Models\Traits\Taggable;
use Hifone\Presenters\ThreadPresenter;
use Hifone\Services\Tag\TaggableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Input;
use McCool\LaravelAutoPresenter\HasPresenter;
use Venturecraft\Revisionable\RevisionableTrait;

class Notice extends Model
{

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
        'user_id',
        'title',
        'content',
        'type',
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        /*'title'        => 'required|min:2',
        'body'         => 'required|min:2',
        'node_id'      => 'required|int',
        'user_id'      => 'required|int',*/
    ];

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return;
        }

        return  $query->where(function ($query) use ($search) {
            $query->where('title', 'LIKE', "%$search%");
        });
    }



}
