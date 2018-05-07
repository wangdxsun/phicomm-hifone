<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hifone\Services\Tag;

use Hifone\Models\Tag;

class AddTag
{

    public function attach(TaggableInterface $taggable, $tags)
    {
        if (!is_array($tags)) {
            $tags = explode(',', $tags);
        }

        $taggable->tags()->sync($tags);

        $this->updatecount($tags);
    }

    protected function updateCount($ids)
    {
        Tag::whereIn('id', $ids)->get()->map(function ($tag) {
            $count = $tag->threads()->count();
            $tag->update(['count' => $count]);
        });
    }
}
