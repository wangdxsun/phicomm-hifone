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
use Hifone\Models\Scopes\ForUser;
use Hifone\Models\Scopes\Recent;
use Hifone\Presenters\ReplyPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;
use Venturecraft\Revisionable\RevisionableTrait;

class Reply extends Model implements HasPresenter
{
    use ValidatingTrait, ForUser, Recent, RevisionableTrait, SoftDeletes;

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'body',
        'user_id',
        'thread_id',
        'body_original',
    ];

    protected $dates = ['deleted_at'];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'thread_id' => 'required|int',
        'body'      => 'required|max:1024',
        'user_id'   => 'int',
    ];

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'object');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lastOpUser()
    {
        return $this->belongsTo(User::class, 'last_op_user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', '>=', 0);
    }

    public function scopeAudit($query)
    {
        return $query->where('status', -2);//审核中
    }

    public function scopeTrash($query)
    {
        return $query->where('status', -1)->orWhere('status', -5);//回收站
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return ReplyPresenter::class;
    }

    public function getPinAttribute()
    {
        return $this->order > 0 ? 'fa fa-thumb-tack text-danger' : 'fa fa-thumb-tack';
    }

    public function getReportAttribute()
    {
        return $this->body;
    }

    public function getUrlAttribute()
    {
        return $this->thread->url . '#reply' . $this->id;
    }
}
