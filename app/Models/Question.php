<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'quiz_id',
        'question_text',
        'options',
        'correct_option_index',
        'time_limit_seconds',
    ];

    protected $casts = [
        'options' => 'array', // Converti automaticamente il JSON in array PHP
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
