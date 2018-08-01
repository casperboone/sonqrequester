<?php

namespace Tests\Feature;

use App\SongRequest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestSongTest extends TestCase
{
    use RefreshDatabase;

    // Requirements
    // - A user can add 1 request each 5 minutes --> determined via session?

    /** @test */
    public function a_user_can_request_a_song()
    {
        $response = $this->post('/requests', [
            'name' => 'Casper',
            'track' => 'New Shoes',
            'artist' => 'Paolo Nutini'
        ]);

        $response->assertStatus(200);

        $latestRequest = SongRequest::firstOrFail();
        $this->assertEquals('Casper', $latestRequest->name);
        $this->assertEquals('New Shoes', $latestRequest->track);
        $this->assertEquals('Paolo Nutini', $latestRequest->artist);
    }

    /** @test */
    public function a_user_is_required_to_give_his_name()
    {
        $response = $this->postJson('/requests', [
            'artist' => 'Paolo Nutini',
            'track' => 'New Shoes',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    /** @test */
    public function a_user_is_required_to_give_a_track_name()
    {
        $response = $this->postJson('/requests', [
            'name' => 'Casper',
            'artist' => 'Paolo Nutini'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('track');
    }

    /** @test */
    public function a_user_is_required_to_give_an_artist_name()
    {
        $response = $this->postJson('/requests', [
            'name' => 'Casper',
            'track' => 'New Shoes',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('artist');
    }
}
