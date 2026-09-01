<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class CommunityIndex extends Component
{
    public string $search = '';
    public string $activeCategory = 'all'; // 'all', 'anime', 'manga', 'off-topic'

    // ID del server Discord (sostituisci con il tuo Guild ID)
    public string $discordServerId = 'YOUR_DISCORD_SERVER_ID';

    public function render()
    {
        // Ricerca utenti dinamica o lista ultimi iscritti
        $usersQuery = User::where('id', '!=', auth()->id())
            ->with('profile');

        if (strlen(trim($this->search)) >= 2) {
            $usersQuery->where('name', 'like', '%' . $this->search . '%');
        }

        $users = $usersQuery->latest()->take(8)->get();

        return view('components.community.community-index', [
            'users' => $users,
        ])->layout('components.layouts.app');
    }
}
