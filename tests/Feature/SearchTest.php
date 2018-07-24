<?php

namespace Tests\Feature;

use App\SongRequest;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_search_a_song()
    {
        $response = $this->postJson('/search', [
            'query' => 'New Shoes'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            [
                'artist' => 'Paolo Nutini',
                'track' => 'New Shoes',
            ]
        ]);
    }

    /** @test */
    public function a_user_can_enter_an_empty_search_query()
    {
        $response = $this->postJson('/search', [
            'query' => ''
        ]);

        $response->assertStatus(200);
        $response->assertJson([]);
    }
}
