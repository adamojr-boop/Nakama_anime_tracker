<?php

namespace App\Livewire;

use App\Models\Quiz;
use Livewire\Component;

class QuizHub extends Component
{
    public function render()
    {
        $quizzes = Quiz::query()
            ->withCount('questions')
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.quiz-hub', [
            'quizzes' => $quizzes,
        ]);
    }
}
