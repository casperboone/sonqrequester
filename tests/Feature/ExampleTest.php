<?php

namespace Tests\Feature;

use App\SongRequest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_request_a_song()
    {
        $response = $this->post('/requests', [
            'track' => 'New Shoes',
            'artist' => 'Paolo Nutini'
        ]);

        $response->assertStatus(200);

        $latestRequest = SongRequest::firstOrFail();
        $this->assertEquals('New Shoes', $latestRequest->track);
        $this->assertEquals('Paolo Nutini', $latestRequest->artist);
    }

    /** @test */
    public function a_user_is_required_to_give_a_track_name()
    {
        $response = $this->postJson('/requests', [
            'artist' => 'Paolo Nutini'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('track');
    }

    /** @test */
    public function a_user_is_required_to_give_an_artist_name()
    {
        $response = $this->postJson('/requests', [
            'track' => 'New Shoes',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('artist');
    }
}
