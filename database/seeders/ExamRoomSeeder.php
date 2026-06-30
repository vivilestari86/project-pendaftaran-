<?php

namespace Database\Seeders;

use App\Models\ExamRoom;
use Illuminate\Database\Seeder;

class ExamRoomSeeder extends Seeder
{
    public function run(): void
    {
        collect([
            ['name' => 'CBT 1', 'capacity' => 60],
            ['name' => 'CBT 2', 'capacity' => 20],
            ['name' => 'CBT 3', 'capacity' => 20],
            ['name' => 'CBT 4', 'capacity' => 20],
            ['name' => 'IOT', 'capacity' => 25],
            ['name' => 'Data Science', 'capacity' => 25],
            ['name' => 'Animasi', 'capacity' => 25],
            ['name' => 'HPC', 'capacity' => 25],
            ['name' => 'SOC', 'capacity' => 25],
        ])->each(fn (array $room) => ExamRoom::updateOrCreate(
            ['name' => $room['name']],
            ['capacity' => $room['capacity']],
        ));
    }
}
