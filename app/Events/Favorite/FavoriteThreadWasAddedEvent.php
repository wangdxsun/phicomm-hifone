<?php
namespace Hifone\Events\Favorite;

//收藏帖子，主动操作
use Hifone\Models\User;

class FavoriteThreadWasAddedEvent implements FavoriteEventInterface
{
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}