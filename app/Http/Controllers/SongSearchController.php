<?php

namespace App\Http\Controllers;

use App\LastFm;
use Illuminate\Http\Request;

class SongSearchController extends Controller
{
    public function search(Request $request) {
        return response(LastFm::search($request->input('query', ''), 5));
    }
}
