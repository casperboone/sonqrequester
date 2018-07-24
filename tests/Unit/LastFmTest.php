<?php

namespace Tests\Unit;

use App\LastFm;
use Tests\TestCase;

class LastFmTest extends TestCase
{
    /** @test */
    public function it_can_fuzzy_search_tracks()
    {
        $results = LastFm::search("shoes paolo");

        $this->assertCount(30, $results);
        $this->assertEquals("Paolo Nutini", $results->first()['artist']);
        $this->assertEquals("New Shoes", $results->first()['track']);
        $this->assertStringStartsWith("http", $results->first()['image']);
    }
}
