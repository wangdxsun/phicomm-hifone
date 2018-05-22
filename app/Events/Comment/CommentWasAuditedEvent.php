<?php
namespace Hifone\Events\Comment;

final class CommentWasAuditedEvent implements CommentEventInterface
{
    public $user;
    public $comment;
    function __construct($user, $comment)
    {
        $this->user = $user;
        $this->comment= $comment;
    }
}