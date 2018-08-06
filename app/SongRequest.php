<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SongRequest extends Model
{
    protected $guarded = ['id'];

    protected $attributes = [
        'votes' => 0,
    ];

    protected $casts = [
        'playing_now' => 'bool',
        'playing_next' => 'bool'
    ];

    public function upvote()
    {
        $this->increment('votes');
    }
}
