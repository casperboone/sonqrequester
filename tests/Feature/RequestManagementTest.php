<?php

namespace Tests\Feature;

use App\Events\RequestGotUpdated;
use App\Events\RequestGotVote;
use App\Events\RequestWasArchived;
use App\SongRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestSongTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_remove_the_name_of_the_requester()
    {
        Event::fake();
        $request = factory(SongRequest::class)->create();

        $response = $this->actingAs(factory(User::class)->create())->postJson('/requests/1/remove-name');
        $response->assertStatus(200);

        $request->refresh();
        $this->assertEquals('🙈🙈', $request->name);
        Event::assertDispatched(RequestGotUpdated::class);
    }

    /** @test */
    public function a_visitor_cannot_remove_the_name_of_a_requester()
    {
        factory(SongRequest::class)->create();

        $response = $this->postJson('/requests/1/remove-name');
        $response->assertStatus(401);
    }

    /** @test */
    public function an_admin_can_remove_a_request()
    {
        Event::fake();
        $request = factory(SongRequest::class)->create();

        $response = $this->actingAs(factory(User::class)->create())->deleteJson('/requests/1');
        $response->assertStatus(200);

        $this->assertEquals(0, SongRequest::count());
        Event::assertDispatched(RequestWasArchived::class);
    }

    /** @test */
    public function a_visitor_cannot_remove_a_request()
    {
        factory(SongRequest::class)->create();

        $response = $this->deleteJson('/requests/1');
        $response->assertStatus(401);
    }

    /** @test */
    public function an_admin_can_mark_a_track_as_now_playing()
    {
        Event::fake();
        $request = factory(SongRequest::class)->create(['playing_next' => true]);

        $response = $this->actingAs(factory(User::class)->create())->postJson('/requests/1/play-now');
        $response->assertStatus(200);

        $request->refresh();
        $this->assertTrue($request->playing_now);
        $this->assertFalse($request->playing_next);
        Event::assertDispatched(RequestGotUpdated::class);
    }

    /** @test */
    public function an_admin_can_mark_a_track_as_now_playing_and_unmark_other_tracks()
    {
        $oldRequestA = factory(SongRequest::class)->create(['playing_now' => true]);
        $oldRequestB = factory(SongRequest::class)->create(['playing_now' => true]);
        $request = factory(SongRequest::class)->create();

        $response = $this->actingAs(factory(User::class)->create())->postJson('/requests/'.$request->id.'/play-now');
        $response->assertStatus(200);

        $oldRequestA->refresh();
        $oldRequestB->refresh();
        $request->refresh();
        $this->assertFalse($oldRequestA->playing_now);
        $this->assertFalse($oldRequestB->playing_now);
        $this->assertTrue($request->playing_now);
    }

    /** @test */
    public function a_visitor_cannot_mark_a_request_as_now_playing()
    {
        factory(SongRequest::class)->create();

        $response = $this->postJson('/requests/1/play-now');
        $response->assertStatus(401);
    }

    /** @test */
    public function an_admin_can_mark_a_track_as_coming_up_next()
    {
        Event::fake();
        $request = factory(SongRequest::class)->create(['playing_next' => false]);

        $response = $this->actingAs(factory(User::class)->create())->postJson('/requests/1/play-next');
        $response->assertStatus(200);

        $request->refresh();
        $this->assertTrue($request->playing_next);
        Event::assertDispatched(RequestGotUpdated::class);
    }

    /** @test */
    public function an_admin_can_mark_a_track_as_coming_up_next_and_unmark_other_tracks()
    {
        $oldRequestA = factory(SongRequest::class)->create(['playing_next' => true]);
        $oldRequestB = factory(SongRequest::class)->create(['playing_next' => true]);
        $request = factory(SongRequest::class)->create();

        $response = $this->actingAs(factory(User::class)->create())->postJson('/requests/'.$request->id.'/play-next');
        $response->assertStatus(200);

        $oldRequestA->refresh();
        $oldRequestB->refresh();
        $request->refresh();
        $this->assertFalse($oldRequestA->playing_next);
        $this->assertFalse($oldRequestB->playing_next);
        $this->assertTrue($request->playing_next);
    }

    /** @test */
    public function a_visitor_cannot_mark_a_request_as_coming_up_next()
    {
        factory(SongRequest::class)->create();

        $response = $this->postJson('/requests/1/play-next');
        $response->assertStatus(401);
    }

}
