<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExamRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_exam_room')->withTimestamps();
    }
}
