<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+2 weeks');

        return [
            'exam_room_id' => ExamRoom::factory(),
            'name' => 'Uji Kompetensi ' . fake('id_ID')->city(),
            'start_date' => $startDate,
            'end_date' => $startDate,
        ];
    }
}
