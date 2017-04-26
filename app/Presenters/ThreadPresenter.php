<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Presenters;

use Carbon\Carbon;
use Hifone\Presenters\Traits\TimestampsTrait;
use Hifone\Services\Dates\DateFactory;
use McCool\LaravelAutoPresenter\Facades\AutoPresenter;

class ThreadPresenter extends AbstractPresenter
{
    use TimestampsTrait;

    public function author_url()
    {
        return AutoPresenter::decorate($this->wrappedObject->user)->url;
    }

    /**
     * Convert the presenter instance to an array.
     *
     * @return string[]
     */
    public function toArray()
    {
        return array_merge($this->wrappedObject->toArray(), [
            'created_at' => $this->created_at(),
            'updated_at' => $this->updated_at(),
        ]);
    }
}
