<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\AffinityService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserAffinityBadge extends Component
{
    public int $profileUserId;
    public ?int $affinityPercentage = null;

    public function mount(int $profileUserId): void
    {
        $this->profileUserId = $profileUserId;
        $this->calculateAffinity();
    }

    public function calculateAffinity(): void
    {
        if (! Auth::check() || Auth::id() === $this->profileUserId) {
            return;
        }

        $currentUser = Auth::user();
        $targetUser = User::find($this->profileUserId);

        if ($targetUser) {
            $service = new AffinityService();
            $this->affinityPercentage = $service->calculate($currentUser, $targetUser);
        }
    }

    public function render()
    {
        return view('user_stats.user-affinity-badge');
    }
}