<?php

namespace App\Http\Controllers;

use App\SongRequest;
use Illuminate\Http\Request;

class SongRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'artist' => 'required',
            'track' => 'required',
        ]);

        SongRequest::create($request->only('artist', 'track'));

        return response('OK', 200);
    }
}
