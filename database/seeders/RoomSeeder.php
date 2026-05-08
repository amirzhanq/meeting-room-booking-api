<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Room::create(['name' => 'Green Room']);
        \App\Models\Room::create(['name' => 'Blue Room']);
        \App\Models\Room::create(['name' => 'Large Conference Hall']);
    }
}
