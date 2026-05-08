<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['bookings' => function ($query) {
            $query->where('end_time', '>', now());
        }])->get();

        return RoomResource::collection($rooms);
    }
}
