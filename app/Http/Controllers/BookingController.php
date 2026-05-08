<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('uid')) {
            $query->where('uid', $request->uid);
        }

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        return BookingResource::collection($query->with('room')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'uid' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        $overlap = Booking::where('room_id', $validated['room_id'])
            ->where(function ($query) use ($validated) {
                $query->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'The room is already booked for the selected time slot.',
                'errors' => [
                    'start_time' => ['Overlap detected.']
                ]
            ], 422);
        }

        $booking = Booking::create($validated);

        return new BookingResource($booking->load('room'));
    }
}
