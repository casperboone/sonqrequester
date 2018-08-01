<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Session\Store as SessionStore;

class Visitor
{
    private $session;

    public function __construct(SessionStore $session)
    {
        $this->session = $session;
    }

    public function hasAlreadyVotedFor(SongRequest $songRequest)
    {
        return $this->votes()->contains(function ($votedRequest) use ($songRequest) {
            return $songRequest->is($votedRequest);
        });
    }

    public function registerVote(SongRequest $songRequest)
    {
        $this->session->put('votes', $this->votes()->push($songRequest));
    }

    public function votes()
    {
        return $this->session->get('votes', collect());
    }

    public function isAllowedToRequest() {
        return Carbon::now()->subMinutes(5)->greaterThanOrEqualTo($this->lastRequest());
    }

    public function lastRequest() {
        return $this->session->get('last_request', Carbon::minValue());
    }

    public function registerRequest() {
        return $this->session->put('last_request', Carbon::now());
    }
}
