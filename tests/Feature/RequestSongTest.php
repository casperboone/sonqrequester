<?php

namespace Tests\Feature;

use App\SongRequest;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestSongTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_request_a_song()
    {
        $response = $this->withoutExceptionHandling()->post('/requests', [
            'name' => 'Casper',
            'track' => 'New Shoes',
            'artist' => 'Paolo Nutini'
        ]);

        $response->assertStatus(201);

        $latestRequest = SongRequest::firstOrFail();
        $this->assertEquals('Casper', $latestRequest->name);
        $this->assertEquals('New Shoes', $latestRequest->track);
        $this->assertEquals('Paolo Nutini', $latestRequest->artist);
        $this->assertEquals(true, $response->json('data.owner'));
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

    /** @test */
    public function a_user_cannot_request_twice_within_5_minutes()
    {
        $response = $this->withoutExceptionHandling()->post('/requests', [
            'name' => 'Casper',
            'track' => 'New Shoes',
            'artist' => 'Paolo Nutini'
        ]);

        $response->assertStatus(201);

        $response = $this->post('/requests', [
            'name' => 'Joel',
            'track' => 'Piano Man',
            'artist' => 'Billy Joel'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function a_user_can_request_again_after_5_minutes()
    {
        $response = $this->post('/requests', [
            'name' => 'Casper',
            'track' => 'New Shoes',
            'artist' => 'Paolo Nutini'
        ]);

        $response->assertStatus(201);

        Carbon::setTestNow(Carbon::now()->addMinutes(10));

        $response = $this->post('/requests', [
            'name' => 'Joel',
            'track' => 'Piano Man',
            'artist' => 'Billy Joel'
        ]);

        $response->assertStatus(201);

        $this->assertEquals(2, SongRequest::count());
    }
}
