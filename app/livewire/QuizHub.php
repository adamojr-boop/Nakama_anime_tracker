<?php

namespace App\Livewire;

use App\Models\Quiz;
use Livewire\Component;

class QuizHub extends Component
{
    public function render()
    {
        $quizzes = Quiz::select('id', 'title', 'category', 'description', 'difficulty')
            ->withCount('questions')
            ->get();

        return view('livewire.quiz-hub', [
            'quizzes' => $quizzes,
        ]);
    }
}
