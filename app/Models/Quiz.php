<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'question',
        'correct_answer_id',
        'is_public'
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function correctAnswer(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'correct_answer_id');
    }

}
