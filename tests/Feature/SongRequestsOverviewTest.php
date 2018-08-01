<?php

namespace Tests\Feature;

use App\SongRequest;
use App\Visitor;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_a_song()
    {
        $requestWithOwnership = factory(SongRequest::class)->create([
            'name' => 'Casper',
            'artist' => 'Paolo Nutini',
            'track' => 'New Shoes',
            'image' => 'http://host.com/image.jpg',
            'votes' => 19,
        ]);
        session()->put('requests', collect([$requestWithOwnership]));
        $requestWithVote = factory(SongRequest::class)->create([
            'name' => 'Joel',
            'artist' => 'Billy Joel',
            'track' => 'Piano Man',
            'votes' => 5,
        ]);
       session()->put('votes', collect([$requestWithVote]));
        factory(SongRequest::class)->create([
            'name' => 'Daan',
            'artist' => 'Troye Sivan',
            'track' => 'There For You',
            'votes' => 11,
        ]);

        $response = $this->get('/requests');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'name' => 'Casper',
                    'artist' => 'Paolo Nutini',
                    'track' => 'New Shoes',
                    'image' => 'http://host.com/image.jpg',
                    'votes' => 19,
                    'allowed_to_vote' => true,
                    'owner' => true,
                ],
                [
                    'name' => 'Joel',
                    'artist' => 'Billy Joel',
                    'track' => 'Piano Man',
                    'votes' => 5,
                    'allowed_to_vote' => false,
                    'owner' => false
                ],
                [
                    'name' => 'Daan',
                    'artist' => 'Troye Sivan',
                    'track' => 'There For You',
                    'votes' => 11,
                    'allowed_to_vote' => true,
                    'owner' => false,
                ],
            ]
        ]);
    }
}
