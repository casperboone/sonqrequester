<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'layouts/frontend');

Route::get('requests', 'SongRequestController@index');
Route::post('requests', 'SongRequestController@store');
Route::post('requests/{songRequest}/upvote', 'SongRequestController@upvote');
Route::post('search', 'SongSearchController@search');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('admin', 'HomeController@index')->name('home');
    Route::post('requests/{songRequest}/play-now', 'SongRequestController@markAsPlaying');
    Route::post('requests/{songRequest}/play-next', 'SongRequestController@markAsPlayingNext');
    Route::post('requests/{songRequest}/remove-name', 'SongRequestController@removeName');
    Route::delete('requests/{songRequest}', 'SongRequestController@delete');
});
