<?php

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_booking()
    {
        $room = Room::create(['name' => 'Room 1']);

        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'uid' => 'user1',
            'start_time' => now()->addHour()->toDateTimeString(),
            'end_time' => now()->addHours(2)->toDateTimeString(),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.uid', 'user1');
    }

    public function test_cannot_overlap_bookings()
    {
        $room = Room::create(['name' => 'Room 1']);

        $start = now()->addHours(5)->roundMinute();
        $end = $start->copy()->addHour();

        Booking::create([
            'room_id' => $room->id,
            'uid' => 'user1',
            'start_time' => $start,
            'end_time' => $end,
        ]);

        // Attempt to book with overlap
        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'uid' => 'user2',
            'start_time' => $start->copy()->subMinutes(30)->toDateTimeString(),
            'end_time' => $start->copy()->addMinutes(30)->toDateTimeString(),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_time']);
    }

    public function test_can_filter_bookings()
    {
        $room1 = Room::create(['name' => 'Room 1']);
        $room2 = Room::create(['name' => 'Room 2']);

        Booking::create([
            'room_id' => $room1->id,
            'uid' => 'user1',
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(2),
        ]);

        Booking::create([
            'room_id' => $room2->id,
            'uid' => 'user2',
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(2),
        ]);

        // Filter by user
        $this->getJson('/api/bookings?uid=user1')
            ->assertStatus(200)
            ->assertJsonCount(1);

        // Filter by room
        $this->getJson("/api/bookings?room_id={$room2->id}")
            ->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_validation_errors_for_invalid_times()
    {
        $room = Room::create(['name' => 'Room 1']);

        // End before start
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'uid' => 'user1',
            'start_time' => now()->addHours(2)->toDateTimeString(),
            'end_time' => now()->addHour()->toDateTimeString(),
        ])->assertStatus(422)->assertJsonValidationErrors(['end_time']);

        // Start in the past
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'uid' => 'user1',
            'start_time' => now()->subDay()->toDateTimeString(),
            'end_time' => now()->addHour()->toDateTimeString(),
        ])->assertStatus(422)->assertJsonValidationErrors(['start_time']);
    }

    public function test_boundary_times_do_not_overlap()
    {
        $room = Room::create(['name' => 'Room 1']);

        $mid = now()->addHours(5)->roundMinute();

        // Booking 1: 10:00 - 11:00
        Booking::create([
            'room_id' => $room->id,
            'uid' => 'user1',
            'start_time' => $mid->copy()->subHour(),
            'end_time' => $mid,
        ]);

        // Booking 2: 11:00 - 12:00 (Exactly at the boundary)
        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'uid' => 'user2',
            'start_time' => $mid->toDateTimeString(),
            'end_time' => $mid->copy()->addHour()->toDateTimeString(),
        ]);

        $response->assertStatus(201);
    }

    public function test_fully_contained_overlap()
    {
        $room = Room::create(['name' => 'Room 1']);

        $start = now()->addHours(10)->roundMinute();
        $end = $start->copy()->addHours(5);

        // Big booking: 10:00 - 15:00
        Booking::create([
            'room_id' => $room->id,
            'uid' => 'user1',
            'start_time' => $start,
            'end_time' => $end,
        ]);

        // Small booking inside: 11:00 - 12:00
        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'uid' => 'user2',
            'start_time' => $start->copy()->addHour()->toDateTimeString(),
            'end_time' => $start->copy()->addHours(2)->toDateTimeString(),
        ]);

        $response->assertStatus(422);
    }

    public function test_list_rooms()
    {
        Room::create(['name' => 'Green']);
        Room::create(['name' => 'Blue']);

        $this->getJson('/api/rooms')
            ->assertStatus(200)
            ->assertJsonCount(2);
    }
}
