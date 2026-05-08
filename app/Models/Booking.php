<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['room_id', 'uid', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
