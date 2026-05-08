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
            ->assertJsonPath('uid', 'user1');
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
}
