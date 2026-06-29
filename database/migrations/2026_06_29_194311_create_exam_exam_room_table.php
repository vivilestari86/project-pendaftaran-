<?php

use App\Models\Exam;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_exam_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('exam_room_id')->constrained('exam_rooms')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['exam_id', 'exam_room_id']);
        });

        Exam::query()
            ->whereNotNull('exam_room_id')
            ->get(['id', 'exam_room_id'])
            ->each(function (Exam $exam) {
                DB::table('exam_exam_room')->insertOrIgnore([
                    'exam_id' => $exam->id,
                    'exam_room_id' => $exam->exam_room_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_exam_room');
    }
};
