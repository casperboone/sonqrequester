<?php

namespace App;


use LastFmApi\Api\AuthApi;
use LastFmApi\Api\TrackApi;

class LastFm
{
    public static function search($query, $limit = 30) {
        try {
            $auth = new AuthApi('setsession', ['apiKey' => config('services.lastfm.key')]);

            $api = new TrackApi($auth);

            $result = $api->search(["track" => $query, "limit" => $limit]);

            return collect($result['results'])
                ->map(function ($track) {
                    return [
                        'artist' => $track['artist'],
                        'track' => $track['name'],
                        'image' => $track['image']['medium']
                    ];
                });
        } catch (\Exception $exception) {
            return collect();
        }
    }
}
