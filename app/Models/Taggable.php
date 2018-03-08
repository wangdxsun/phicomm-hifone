<?php
namespace Hifone\Models;

class Taggable extends BaseModel
{
    protected $table = 'taggables';
    public function taggable()
    {
        return $this->morphTo();
    }
}