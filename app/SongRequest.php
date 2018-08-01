<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SongRequest extends Model
{
    protected $guarded = ['id'];

    public function upvote()
    {
        $this->increment('votes');
    }
}
