<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class PlayQuiz extends Component
{
    public Quiz $quiz;
    public $questions;
    public int $currentIndex = 0;
    public ?int $selectedOption = null;
    public int $score = 0;
    public int $correctAnswersCount = 0;
    public bool $isFinished = false;
    public int $timeLeft = 15;
    public array $unlockedBadges = []; // 👈 Memorizza eventuali badge sbloccati

    public function mount($quizId)
    {
        $this->quiz = Quiz::with('questions')->findOrFail($quizId);
        $this->questions = $this->quiz->questions;

        if ($this->questions->isNotEmpty()) {
            $this->timeLeft = $this->questions[0]->time_limit_seconds;
        }
    }

    public function selectOption($index)
    {
        if ($this->selectedOption !== null) return; // Impedisce modifiche post-selezione
        $this->selectedOption = $index;
    }

    public function nextQuestion()
    {
        $currentQuestion = $this->questions[$this->currentIndex];
        // Verifica risposta corretta
        if ($this->selectedOption === $currentQuestion->correct_option_index) {
            $this->correctAnswersCount++;
            // Punteggio: 100 punti base + bonus tempo rimanente
            $this->score += 100 + ($this->timeLeft * 5);
        }

        $this->selectedOption = null;
        $this->currentIndex++;

        if ($this->currentIndex >= $this->questions->count()) {
            $this->finishQuiz();
        } else {
            $this->timeLeft = $this->questions[$this->currentIndex]->time_limit_seconds;
        }
    }

    public function finishQuiz()
    {
        $this->isFinished = true;

        if (Auth::check()) {
            QuizAttempt::create([
                'user_id' => Auth::id(),
                'quiz_id' => $this->quiz->id,
                'score' => $this->score,
                'correct_answers' => $this->correctAnswersCount,
                'total_questions' => $this->questions->count(),
            ]);

            // 👈 Svuota la cache della classifica in modo che la prossima richiesta legga i nuovi punteggi!
            Cache::forget('global_quiz_leaderboard');

            // Integrazione Badge
            if ($this->correctAnswersCount === $this->questions->count()) {
                $badgeService = app(BadgeService::class);
                $badgeService->grantBadge(
                    Auth::user(),
                    'Otaku Sensei 🧠',
                    'Hai risposto correttamente a tutte le domande di un Quiz!',
                    '🧠'
                );
            }
        }
    }

    public function render()
    {
        return view('components.trivia.play-quiz')
            ->layout('components.layouts.app');
    }
}
