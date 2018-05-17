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

class Credit extends BaseModel
{
    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = ['user_id', 'rule_id', 'balance', 'body', 'frequency_tag','created_at','object_type', 'object_id'];

    protected $hidden = [
        'id',
        'user_id',
        'rule_id',
        'frequency_tag',
        'balance',
        'updated_at'
    ];
    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'user_id'    => 'required|int',
        'rule_id'    => 'required|int',
    ];

    /**
     * Overrides the models boot method.
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($credit) {
            if (!$credit->frequency_tag) {
                $credit->frequency_tag = self::generateFrequencyTag();
            }
        });
    }

    /**
     * Credits can belong to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A credit belongs to a credit rule.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rule()
    {
        return $this->belongsTo(CreditRule::class, 'rule_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'object');
    }

    /**
     * Returns a frequency tag.
     *
     * @return string
     */
    public static function generateFrequencyTag()
    {
        return date('Ymd');
    }

    public function getRewardAttribute()
    {
        $reward = $this->body ?: $this->rule->reward;
        if ($reward > 0) {
            $prefix = '<strong class="text-success">+';
        } else {
            $prefix = '<strong class="text-danger">';
        }

        return $prefix.number_format($reward, 1).'</strong>';
    }
}
