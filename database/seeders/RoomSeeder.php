<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create(['name' => 'Green Room']);
        Room::create(['name' => 'Blue Room']);
        Room::create(['name' => 'Large Conference Hall']);
    }
}
