<?php

namespace Tests\Feature;

use App\SongRequest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_upvote_a_track()
    {
        factory(SongRequest::class)->create([
            'votes' => 0,
        ]);

        $response = $this->postJson('/requests/1/upvote');

        $response->assertStatus(200);

        $this->assertEquals(1, SongRequest::first()->votes);
    }

    /** @test */
    public function a_user_cannot_upvote_a_track_more_than_once()
    {
        factory(SongRequest::class)->create([
            'votes' => 0,
        ]);

        $this->postJson('/requests/1/upvote');

        $response = $this->postJson('/requests/1/upvote');

        $this->assertEquals(1, SongRequest::first()->votes);
        $response->assertStatus(422);
    }
}
