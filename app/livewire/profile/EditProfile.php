<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class EditProfile extends Component
{
    use WithFileUploads;

    public $avatar;
    public $banner;
    public string $avatarFrame = 'default';
    public string $bannerPattern = 'pattern-1';
    public string $bio = '';

    // Social Links
    public string $discord = '';
    public string $twitter = '';
    public string $instagram = '';
    public string $mal = '';

    public function mount(): void
    {
        $profile = auth()->user()->profile;

        if ($profile) {
            $this->avatarFrame = $profile->avatar_frame ?? 'default';
            $this->bannerPattern = $profile->banner_pattern ?? 'pattern-1';
            $this->bio = $profile->bio ?? '';

            $socials = $profile->social_links ?? [];
            $this->discord = $socials['discord'] ?? '';
            $this->twitter = $socials['twitter'] ?? '';
            $this->instagram = $socials['instagram'] ?? '';
            $this->mal = $socials['mal'] ?? '';
        }
    }

    public function removeBannerImage(): void
    {
        $profile = auth()->user()->profile;
        if ($profile && $profile->banner) {
            Storage::disk('public')->delete($profile->banner);
            $profile->banner = null;
            $profile->save();
        }
        $this->banner = null;
        session()->flash('success', 'Immagine di copertina rimossa! Ora è attivo il pattern.');
    }

    public function save()
    {
        $this->validate([
            'avatar' => 'nullable|image|max:2048', // max 2MB
            'banner' => 'nullable|image|max:4096', // max 4MB
            'bio' => 'nullable|string|max:300',
            'discord' => 'nullable|string|max:50',
            'twitter' => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:100',
            'mal' => 'nullable|string|max:100',
        ]);

        $user = auth()->user();
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        // Caricamento Avatar
        if ($this->avatar) {
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $avatarPath = $this->avatar->store('avatars', 'public');
            $profile->avatar = $avatarPath;
            $this->avatar = null;
        }

        // Caricamento Banner
        if ($this->banner) {
            if ($profile->banner) {
                Storage::disk('public')->delete($profile->banner);
            }
            $bannerPath = $this->banner->store('banners', 'public');
            $profile->banner = $bannerPath;
            $this->banner = null;
        }

        $profile->avatar_frame = $this->avatarFrame;
        $profile->banner_pattern = $this->bannerPattern;
        $profile->bio = $this->bio;
        $profile->social_links = [
            'discord' => $this->discord,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            'mal' => $this->mal,
        ];

        $profile->save();

        session()->flash('success', 'Profilo aggiornato con successo!');

        // REINDIRIZZO DIRETTAMENTE AL PROFILO PUBBLICO/PRIVATO DELL'UTENTE
        return redirect()->route('profile.show');
    }

    public function render()
    {
        return view('components.profile.edit-profile')->layout('layouts.app');
    }
}
