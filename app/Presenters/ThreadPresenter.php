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

    public function icons()
    {
        $icons = [];
        $this->wrappedObject->is_excellent && $icons[] = 'fa fa-diamond text-danger';
        $this->wrappedObject->order > 0 && $icons[] = 'fa fa-thumb-tack text-danger';
        return $icons;
    }

    public function pin()
    {
        return $this->wrappedObject->order > 0 ? 'fa fa-thumb-tack text-danger' : 'fa fa-thumb-tack';
    }

    public function excellent()
    {
        return $this->wrappedObject->is_excellent ? 'fa fa-diamond text-danger' : 'fa fa-diamond';
    }

    public function sink()
    {
        return $this->wrappedObject->order < 0 ? 'fa fa-anchor text-danger' : 'fa fa-anchor';
    }

    /**
     * Highlight for threads of today.
     *
     * @return string|null
     */
    public function highlight()
    {
        return (Carbon::now()->format('Ymd') == app(DateFactory::class)->make($this->wrappedObject->updated_at)->format('Ymd')) ? 'text-danger' : null;
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
