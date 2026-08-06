<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class UserSearch extends Component
{
    public string $query = '';

    public function render()
    {
        $users = collect();

        if (strlen(trim($this->query)) >= 2) {
            $users = User::where('id', '!=', auth()->id())
                ->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->query . '%')
                        ->orWhereHas('profile', function ($p) {
                            $p->where('username', 'like', '%' . $this->query . '%');
                        });
                })
                ->with('profile')
                ->take(5)
                ->get();
        }

        return view('livewire.user-search', [
            'users' => $users,
        ]);
    }
}
