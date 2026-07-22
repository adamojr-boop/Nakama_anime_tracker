<?php

namespace App\Livewire;

use App\Models\BingeSession;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class UserDashboard extends Component
{
    public function render()
    {
        $userId = auth()->id();

        $maxBinge = Cache::remember("user_{$userId}_max_binge", 300, function () use ($userId) {
            return BingeSession::where('user_id', $userId)
                ->max('episodes_watched') ?? 0;
        });
        $leaderboard = Cache::remember('global_quiz_leaderboard', 600, function () {
            return QuizAttempt::query()
                ->select('users.name', DB::raw('SUM(quiz_attempts.score) as total_score'))
                ->join('users', 'users.id', '=', 'quiz_attempts.user_id')
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_score')
                ->take(10)
                ->get()
                ->toArray();
        });

        return view('livewire.user-dashboard', [
            'maxBinge'      => $maxBinge,
            'leaderboard'   => $leaderboard,
        ])->layout('components.layouts.app');
    }
}
