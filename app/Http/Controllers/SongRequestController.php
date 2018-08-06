<?php

namespace App\Http\Controllers;

use App\Events\RequestGotUpdated;
use App\Events\RequestGotVote;
use App\Events\RequestWasDeleted;
use App\Events\SongWasRequested;
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
            return response(['message' => 'You already made a request just now, please wait a few minutes before your next request.'], 403);
        }

        $request->validate([
            'name' => 'required',
            'artist' => 'required',
            'track' => 'required',
            'image' => 'url'
        ]);

        $songRequest = SongRequest::create($request->only('name', 'artist', 'track', 'image'));
        $visitor->registerRequest($songRequest);

        broadcast(new SongWasRequested($songRequest))->toOthers();

        return new SongRequestResource($songRequest);
    }

    public function upvote(Visitor $visitor, SongRequest $songRequest)
    {
        if ($visitor->hasAlreadyVotedFor($songRequest)) {
            return response(['message' => 'You already voted for this track.'], 403);
        }

        $songRequest->upvote();
        $visitor->registerVote($songRequest);

        broadcast(new RequestGotVote($songRequest));

        return new SongRequestResource($songRequest);
    }

    public function removeName(SongRequest $songRequest)
    {
        $songRequest->update(['name' => '🙈🙈']);

        broadcast(new RequestGotUpdated($songRequest));

        return new SongRequestResource($songRequest);
    }

    public function delete(SongRequest $songRequest)
    {
        $songRequest->delete();

        broadcast(new RequestWasDeleted($songRequest));

        return new SongRequestResource($songRequest);
    }

    public function markAsPlaying(SongRequest $songRequest)
    {
        $songRequest->markAsPlaying();

        broadcast(new RequestGotUpdated($songRequest));

        return new SongRequestResource($songRequest);
    }

    public function markAsPlayingNext(SongRequest $songRequest)
    {
        $songRequest->markAsPlayingNext();

        broadcast(new RequestGotUpdated($songRequest));

        return new SongRequestResource($songRequest);
    }
}
