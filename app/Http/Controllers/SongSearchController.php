<?php

namespace App\Http\Controllers;

use App\KarafunSearch;
use Illuminate\Http\Request;

class SongSearchController extends Controller
{
    public function search(Request $request) {
        return response(KarafunSearch::search($request->input('query', ''), 5));
    }
}
