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

    public function markAsPlaying()
    {
        static::where('id', '!=', $this->id)->update(['playing_now' => false]);

        $this->update([
            'playing_now' => true,
            'playing_next' => false,
        ]);
    }

    public function markAsPlayingNext()
    {
        static::where('id', '!=', $this->id)->update(['playing_next' => false]);

        $this->update(['playing_next' => true]);
    }
}
