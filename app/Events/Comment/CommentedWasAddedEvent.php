<?php
namespace Hifone\Events\Comment;

//回答被回复
final class CommentedWasAddedEvent implements CommentEventInterface
{
    public $user;
    public $answer;
    function __construct($user, $answer)
    {
        $this->user = $user;
        $this->answer= $answer;
    }
}