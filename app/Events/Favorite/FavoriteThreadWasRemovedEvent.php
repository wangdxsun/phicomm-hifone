<?php
namespace Hifone\Events\Favorite;

//取消收藏帖子，主动操作
use Hifone\Models\User;

class FavoriteThreadWasRemovedEvent implements FavoriteEventInterface
{
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}