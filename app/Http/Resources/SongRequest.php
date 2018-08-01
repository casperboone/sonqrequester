<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SongRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'artist' => $this->artist,
            'track' => $this->track,
            'votes' => (int)$this->votes,
            'allowed_to_vote' => !$request->visitor()->hasAlreadyVotedFor($this->resource),
        ];
    }
}
