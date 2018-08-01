<?php

namespace App\Http\Controllers;

use App\SongRequest;
use App\Visitor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SongRequestController extends Controller
{
    public function store(Visitor $visitor, Request $request)
    {
        if( !$visitor->isAllowedToRequest()) {
            return response("You can request a song again in a few minutes.", 403);
        }

        $request->validate([
            'name' => 'required',
            'artist' => 'required',
            'track' => 'required',
        ]);

        SongRequest::create($request->only('name', 'artist', 'track'));
        $visitor->registerRequest();

        return response('OK', 200);
    }

    public function upvote(Visitor $visitor, SongRequest $songRequest)
    {
        if ($visitor->hasAlreadyVotedFor($songRequest)) {
            return response('You already voted for this track.', 403);
        }

        $songRequest->upvote();
        $visitor->registerVote($songRequest);

        return response('OK', 200);
    }
}
