<?php

namespace Database\Factories;

use App\Models\ExamRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamRoom>
 */
class ExamRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Lokal ' . fake('id_ID')->unique()->bothify('??-##'),
            'capacity' => fake()->numberBetween(10, 30),
        ];
    }
}
