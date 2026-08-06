@php
// Gradiente dinamico per il Pattern scelto
$patternClass = match($bannerPattern) {
'pattern-2' => 'linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%)', // Cherry Blossom
'pattern-3' => 'linear-gradient(135deg, #f12711 0%, #f5af19 100%)', // Shonen Aura
'pattern-4' => 'linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%)', // Minimal Dark
default => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)', // Cyberpunk Tokyo / pattern-1
};

// Priorità anteprima Banner:
// 1. Nuova immagine temporanea selezionata
// 2. Se non stiamo caricando una nuova immagine e l'utente ha un banner salvato nel DB, usa quello.
// 3. Altrimenti usa NULL (che farà scattare $patternClass)
$bannerUrl = null;
if ($banner) {
try {
$bannerUrl = $banner->temporaryUrl();
} catch (\Exception $e) {
$bannerUrl = null;
}
} elseif (auth()->user()->profile && auth()->user()->profile->banner) {
$bannerUrl = asset('storage/' . auth()->user()->profile->banner);
}

// Avatar
$avatarUrl = null;
if ($avatar) {
try {
$avatarUrl = $avatar->temporaryUrl();
} catch (\Exception $e) {
$avatarUrl = null;
}
} elseif (auth()->user()->profile && auth()->user()->profile->avatar) {
$avatarUrl = asset('storage/' . auth()->user()->profile->avatar);
} else {
$avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0d6efd&color=fff';
}

// Stile Bordo Avatar
$frameStyle = match($avatarFrame) {
'gold' => 'border: 4px solid #ffd700; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6);',
'neon' => 'border: 4px solid #00f3ff; box-shadow: 0 0 15px rgba(0, 243, 255, 0.6);',
'fire' => 'border: 4px solid #ff4757; box-shadow: 0 0 15px rgba(255, 71, 87, 0.7);',
default => 'border: 3px solid #ffffff;',
};
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-white shadow border-secondary">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">⚙️ Personalizza Profilo</h4>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-light btn-sm">Vedi Profilo</a>
                </div>

                <div class="card-body">
                    @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- BOX ANTEPRIMA VISIVA -->
                    <div class="mb-4 text-center">
                        <label class="form-label small text-muted d-block text-start fw-bold">Anteprima Live Copertina</label>
                        <div class="rounded-3 shadow-sm position-relative overflow-hidden"
                            style="height: 140px; background: {{ $bannerUrl ? "url('{$bannerUrl}') center/cover no-repeat" : $patternClass }};">

                            <div class="position-absolute bottom-0 start-0 m-3 d-flex align-items-center">
                                <img src="{{ $avatarUrl }}" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover; background-color: #1a1a1a; {{ $frameStyle }}">
                                <span class="ms-3 fw-bold fs-5 text-white text-shadow">{{ $name ?: auth()->user()->name }}</span>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="name" class="form-label text-white fw-bold">Username</label>
                            <input
                                type="text"
                                id="name"
                                wire:model="name"
                                class="form-control bg-dark text-white border-secondary @error('name') is-invalid @enderror"
                                placeholder="Il tuo nome">
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- BANNER & PATTERN -->
                        <div class="mb-4 p-3 rounded bg-secondary bg-opacity-10 border border-secondary border-opacity-25">
                            <label class="form-label fw-bold">Banner Custom</label>
                            <input type="file" wire:model="banner" class="form-control bg-secondary text-white border-0 mb-2" accept="image/*">
                            <div wire:loading wire:target="banner" class="text-info small mb-2">Caricamento anteprima banner...</div>
                            @error('banner') <span class="text-danger small d-block mb-2">{{ $message }}</span> @enderror

                            @if(auth()->user()->profile && auth()->user()->profile->banner)
                            <button type="button" wire:click="removeBannerImage" class="btn btn-outline-danger btn-sm mb-3">
                                🗑️ Rimuovi immagine custom (per usare il Pattern sotto)
                            </button>
                            @endif

                            <hr class="border-secondary opacity-25 my-3">

                            <label class="form-label fw-bold mb-1">Pattern Sfの内</label>
                            <p class="small text-white mb-2">Se non hai caricato un'immagine personalizzata, verrà mostrato il pattern scelto qui sotto:</p>

                            <select wire:model.live="bannerPattern" class="form-select bg-secondary text-white border-0">
                                <option value="pattern-1">🌆 Cyberpunk Tokyo</option>
                                <option value="pattern-2">🌸 Cherry Blossom</option>
                                <option value="pattern-3">🔥 Shonen Aura</option>
                                <option value="pattern-4">🌌 Minimal Dark Space</option>
                            </select>
                        </div>

                        <!-- AVATAR & BORDO -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Avatar Profilo</label>
                                <input type="file" wire:model="avatar" class="form-control bg-secondary text-white border-0 mb-2" accept="image/*">
                                <div wire:loading wire:target="avatar" class="text-info small mb-2">Caricamento anteprima avatar...</div>
                                @error('avatar') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bordo / Cornice Avatar</label>
                                <select wire:model.live="avatarFrame" class="form-select bg-secondary text-white border-0">
                                    <option value="default">⚪ Standard (Nessun Bordo)</option>
                                    <option value="gold">🥇 Gold Champion</option>
                                    <option value="neon">⚡ Neon Cyber</option>
                                    <option value="fire">🔥 Super Saiyan Fire</option>
                                </select>
                            </div>
                        </div>
                        <!-- BIOGRAFIA -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Biografia</label>
                            <textarea wire:model="bio" rows="3" class="form-control bg-secondary text-white border-0" placeholder="Scrivi qualcosa su di te..."></textarea>
                            @error('bio') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                        </div>

                        <!-- SOCIAL LINKS -->
                        <h5 class="fw-bold mb-3 border-bottom border-secondary pb-2">🔗 Link Social & Community</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small">Discord (Username)</label>
                                <input type="text" wire:model="discord" class="form-control bg-secondary text-white border-0" placeholder="username#0000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">X / Twitter</label>
                                <input type="text" wire:model="twitter" class="form-control bg-secondary text-white border-0" placeholder="@username">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Instagram</label>
                                <input type="text" wire:model="instagram" class="form-control bg-secondary text-white border-0" placeholder="@username">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">MyAnimeList / AniList</label>
                                <input type="text" wire:model="mal" class="form-control bg-secondary text-white border-0" placeholder="Link profilo">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                            💾 Salva Modifiche
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>