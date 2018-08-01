<?php

namespace App\Http\Controllers;

use App\SongRequest;
use App\Visitor;
use App\Http\Resources\SongRequest as SongRequestResource;
use Illuminate\Http\Request;

class SongRequestController extends Controller
{
    public function index()
    {
        return SongRequestResource::collection(SongRequest::all());
    }
    
    public function store(Visitor $visitor, Request $request)
    {
        if (!$visitor->isAllowedToRequest()) {
            return response("You can request a song again in a few minutes.", 403);
        }

        $request->validate([
            'name' => 'required',
            'artist' => 'required',
            'track' => 'required',
        ]);

        $songRequest = SongRequest::create($request->only('name', 'artist', 'track'));
        $visitor->registerRequest();

        return new SongRequestResource($songRequest);
    }

    public function upvote(Visitor $visitor, SongRequest $songRequest)
    {
        if ($visitor->hasAlreadyVotedFor($songRequest)) {
            return response('You already voted for this track.', 403);
        }

        $songRequest->upvote();
        $visitor->registerVote($songRequest);

        return new SongRequestResource($songRequest);
    }
}
