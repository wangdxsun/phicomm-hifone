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

use Hifone\Models\Pm\Meta;

class Pm extends BaseModel
{
    const INBOX = 1;

    const OUTBOX = 2;

    protected $fillable = ['root_id', 'user_id', 'author_id', 'meta_id', 'folder'];

    /**
     * A Pm belongs to a meta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function meta()
    {
        return $this->belongsTo(Meta::class);
    }

    /**
     * A Pm belongs to a meta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A Pm belongs to a author.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
