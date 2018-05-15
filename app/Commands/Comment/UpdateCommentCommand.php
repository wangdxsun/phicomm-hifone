<?php
namespace Hifone\Commands\Comment;

final class UpdateCommentCommand
{
    public $comment;
    public $data;

    public function __construct($comment, $data)
    {
        $this->comment = $comment;
        $this->data = $data;
    }
}