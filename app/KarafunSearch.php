<?php

namespace App;


use LastFmApi\Api\AuthApi;
use LastFmApi\Api\TrackApi;

class KarafunSearch
{
    public static function search($query, $limit = 30) {
        try {
            $response = file_get_contents(
                "https://www.karafun.nl/000000?type=song_list&filter=sc_"
                . urlencode($query)
                . "&offset=0&limit=$limit"
            );

            $result = json_decode($response, true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new \Exception("JSON could not be parsed");
            }

            return collect($result['songs'])
                ->map(function ($track) {
                    return [
                        'track_id' => $track['id'],
                        'artist' => $track['artist']['name'],
                        'track' => $track['name'],
                        'image' => $track['img']
                    ];
                });
        } catch (\Exception $exception) {
            return collect();
        }
    }
}
