<?php
namespace Hifone\Events\Favorite;

//收藏，主动操作
use Hifone\Models\User;

class FavoriteWasAddedEvent implements FavoriteEventInterface
{
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}