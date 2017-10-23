<?php

namespace Hifone\Http\Bll;

use AltThree\Validator\ValidationException;
use Hifone\Commands\Favorite\AddFavoriteCommand;
use Hifone\Models\Thread;

class FavoriteBll extends BaseBll
{

    public function createOrDelete(Thread $thread)
    {
        try {
            dispatch(new AddFavoriteCommand($thread));
        } catch (ValidationException $e) {
            return $e->getMessageBag();
        }

        return ['status' => 1];
    }
}