<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamSession>
 */
class ExamSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'exam_id' => Exam::factory(),
            'order' => fake()->numberBetween(1, 3),
            'start_time' => '08:00',
            'end_time' => '10:00',
        ];
    }
}
