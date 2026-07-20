<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Badge;
use Illuminate\Support\Facades\Auth;

class UserBadgesWidget extends Component
{
    public function render()
    {
        $user = Auth::user();
        // Recuperiamo tutti i badge del sistema
        $allBadges = Badge::all();
        // Recuperiamo gli ID dei badge sbloccati dall'utente corrente e le relative date
        $unlockedBadges = $user ? $user->badges()->pluck('unlocked_at', 'badge_id')->toArray() : [];
        // Calcolo per il counter
        $totalBadges = $allBadges->count();
        $unlockedCount = count($unlockedBadges);

        return view('user_stats.user-badges-widget', [
            'allBadges' => $allBadges,
            'unlockedBadges' => $unlockedBadges,
            'totalBadges' => $totalBadges,
            'unlockedCount' => $unlockedCount,
        ]);
    }
}
