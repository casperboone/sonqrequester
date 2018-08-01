<?php

namespace App\Http\Controllers;

use App\SongRequest;
use App\Visitor;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SongRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'artist' => 'required',
            'track' => 'required',
        ]);

        SongRequest::create($request->only('name', 'artist', 'track'));

        return response('OK', 200);
    }

    public function upvote(Visitor $visitor, SongRequest $songRequest)
    {
        if ($visitor->hasAlreadyVotedFor($songRequest)) {
            throw ValidationException::withMessages(['visitor' => 'You already voted for this track.']);
        }

        $visitor->registerVote($songRequest);
        $songRequest->upvote();

        return response('OK', 200);
    }
}
