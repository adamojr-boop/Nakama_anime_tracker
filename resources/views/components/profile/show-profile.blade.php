@php
$profile = $user->profile;

// Gestione Banner / Pattern di fallback
$bannerUrl = $profile && $profile->banner ? asset('storage/' . $profile->banner) : null;
$patternClass = match($profile->banner_pattern ?? 'pattern-1') {
'pattern-2' => 'linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%)', // Cherry Blossom
'pattern-3' => 'linear-gradient(135deg, #f12711 0%, #f5af19 100%)', // Shonen Aura
'pattern-4' => 'linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%)', // Minimal Dark
default => 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)', // Cyberpunk / Default
};

// Gestione Avatar
$avatarUrl = $profile && $profile->avatar
? asset('storage/' . $profile->avatar)
: 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0d6efd&color=fff';

// Mappatura Visiva Bordi Avatar
$frameStyle = match($profile->avatar_frame ?? 'default') {
'gold' => 'border: 4px solid #ffd700; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6);',
'neon' => 'border: 4px solid #00f3ff; box-shadow: 0 0 15px rgba(0, 243, 255, 0.6);',
'fire' => 'border: 4px solid #ff4757; box-shadow: 0 0 15px rgba(255, 71, 87, 0.7);',
default => 'border: 3px solid #ffffff;',
};

$socials = is_array($profile->social_links ?? null) ? array_filter($profile->social_links) : [];
@endphp

<div class="container py-4">
    <!-- HEADER PROFILO -->
    <div class="card bg-dark text-white border-0 shadow-lg overflow-hidden mb-4 rounded-4">
        <div style="height: 220px; background: {{ $bannerUrl ? "url('{$bannerUrl}') center/cover no-repeat" : $patternClass }}; position: relative;">
            @if(auth()->id() === $user->id)
            <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-dark bg-opacity-75 text-white position-absolute top-0 end-0 m-3 shadow border-secondary">
                ✏️ Modifica Profilo
            </a>
            @endif

            @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show m-3 position-absolute top-0 start-0 z-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>

        <div class="card-body position-relative pt-0 px-4 pb-4">
            <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end mb-3" style="margin-top: -60px;">
                <img src="{{ $avatarUrl }}" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover; background-color: #1a1a1a; {{ $frameStyle }}">

                <div class="ms-md-4 mt-3 mt-md-0 text-center text-md-start flex-grow-1">
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div>
                            <h2 class="fw-bold mb-0 text-white">{{ $user->name }}</h2>
                            <span class="text-muted small">@nakama_{{ $user->id }} • Iscritto dal {{ $user->created_at->format('M Y') }}</span>
                        </div>

                        @if(auth()->id() !== $user->id)
                        <div class="mt-3 mt-md-0">
                            <button class="btn btn-primary btn-sm px-4 fw-bold rounded-pill">
                                ➕ Segui
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="my-3">
                <p class="card-text text-light-50 mb-0" style="max-width: 700px; line-height: 1.6;">
                    {{ $profile->bio ?? 'Nessuna biografia aggiunta.' }}
                </p>
            </div>

            @if(!empty($socials))
            <div class="d-flex flex-wrap gap-2 pt-2 border-top border-secondary border-opacity-25">
                @if(!empty($socials['discord']))
                <span class="badge bg-secondary bg-opacity-50 text-white p-2 fw-normal">👾 {{ $socials['discord'] }}</span>
                @endif
                @if(!empty($socials['twitter']))
                <a href="https://x.com/{{ ltrim($socials['twitter'], '@') }}" target="_blank" class="badge bg-primary bg-opacity-75 text-white text-decoration-none p-2">🐦 Twitter</a>
                @endif
                @if(!empty($socials['instagram']))
                <a href="https://instagram.com/{{ ltrim($socials['instagram'], '@') }}" target="_blank" class="badge bg-danger bg-opacity-75 text-white text-decoration-none p-2">📸 Instagram</a>
                @endif
                @if(!empty($socials['mal']))
                <a href="{{ $socials['mal'] }}" target="_blank" class="badge bg-info text-dark text-decoration-none p-2">⛩️ AnimeList</a>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- VETRINA DEI PREFERITI (INDEPENDENTE E COLLAPSABILE) -->
    <div x-data="{ open: true }" class="card bg-dark text-white border-secondary shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-dark border-0 d-flex align-items-center justify-content-between p-3">
            <div class="d-flex align-items-center gap-2 cursor-pointer" @click="open = !open">
                <span class="fs-5">⭐</span>
                <h6 class="fw-bold mb-0 text-white">Vetrina dei Preferiti</h6>
                <span class="badge bg-secondary bg-opacity-25 ms-1" x-text="open ? '▲' : '▼'" style="font-size: 10px;"></span>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if(auth()->id() === $user->id)
                <button wire:click="$dispatch('open-favorite-modal')" class="btn btn-xs btn-outline-warning rounded-pill px-3 py-1 fw-bold fs-xs">
                    ✏️ Modifica
                </button>
                @endif
                <button @click="open = !open" type="button" class="btn btn-xs btn-link text-muted p-0 text-decoration-none ms-1">
                    <span x-show="open" class="small">Nascondi</span>
                    <span x-show="!open" class="small">Mostra</span>
                </button>
            </div>
        </div>

        <div x-show="open" x-collapse class="card-body pt-0 px-3 pb-3">
            <div class="row row-cols-5 g-2">
                @for ($i = 1; $i <= 5; $i++)
                    @php
                    $fav=$favoritesBySlot[$i] ?? null;
                    @endphp
                    <div class="col">
                    <div class="card bg-secondary bg-opacity-10 border border-secondary border-opacity-25 rounded-3 overflow-hidden text-center h-100 position-relative" style="min-height: 160px;">
                        @if($fav)
                        <div class="position-relative w-100 h-100 flex-grow-1" style="min-height: 140px;">
                            <img
                                src="{{ $fav->image_display }}"
                                class="w-100 h-100 position-absolute top-0 start-0 object-fit-cover"
                                alt="{{ $fav->title }}"
                                onerror="this.src='https://via.placeholder.com/150x225?text=No+Cover'">
                        </div>
                        <div class="p-1 bg-dark bg-opacity-90 position-relative z-1 border-top border-secondary border-opacity-25">
                            <span class="d-block text-truncate text-white fw-semibold" style="font-size: 10px;" title="{{ $fav->title }}">
                                {{ $fav->title }}
                            </span>
                        </div>
                        @else
                        <div wire:click="$dispatch('open-favorite-modal')" class="d-flex flex-column align-items-center justify-content-center h-100 py-4 text-muted cursor-pointer hover-opacity">
                            <span class="fs-4 opacity-50">+</span>
                            <span style="font-size: 10px;">Slot {{ $i }}</span>
                        </div>
                        @endif
                    </div>
            </div>
            @endfor
        </div>
    </div>
</div>

<!-- TAB NAVIGATION & CONTENUTI -->
<div class="card bg-dark text-white border-secondary shadow-sm rounded-4">
    <div class="card-header border-secondary bg-transparent p-3">
        <ul class="nav nav-pills card-header-pills gap-2">
            <li class="nav-item">
                <button wire:click="setTab('showcase')" class="nav-link {{ $activeTab === 'showcase' ? 'active fw-bold bg-primary text-white' : 'text-secondary' }}">
                    ▶️ In Corso
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="setTab('stats')" class="nav-link {{ $activeTab === 'stats' ? 'active fw-bold bg-primary text-white' : 'text-secondary' }}">
                    📊 Statistiche
                </button>
            </li>
            <li class="nav-item">
                <button wire:click="setTab('badges')" class="nav-link {{ $activeTab === 'badges' ? 'active fw-bold bg-primary text-white' : 'text-secondary' }}">
                    🏆 Trofei & Badge
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <!-- CONTENUTO TAB 1: IN CORSO -->
        @if($activeTab === 'showcase')
        @php
        $watchingAnimes = $watchingAnimes ?? collect();
        @endphp

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0">▶️ In Visione Ora</h5>
            <span class="badge bg-primary rounded-pill">{{ count($watchingAnimes) }} titoli</span>
        </div>

        <div class="row g-3">
            @forelse($watchingAnimes as $anime)
            @php
            $title = is_array($anime) ? ($anime['title'] ?? '') : $anime->title;
            $image = is_array($anime) ? ($anime['image'] ?? $anime['image_url'] ?? '') : ($anime->image_url ?? $anime->image);
            $currentEp = is_array($anime) ? ($anime['episodes_watched'] ?? $anime['watched_episodes'] ?? 0) : ($anime->pivot->episodes_watched ?? 0);
            $totalEp = is_array($anime) ? ($anime['total_episodes'] ?? '?') : ($anime->total_episodes ?? '?');
            @endphp

            <div class="col-6 col-md-3">
                <div class="card bg-dark border-secondary text-white rounded-3 h-100 overflow-hidden shadow-sm">
                    <div class="ratio ratio-3x4 overflow-hidden bg-secondary position-relative">
                        <img src="{{ $image }}" alt="{{ $title }}" class="card-img-top object-fit-cover w-100 h-100" loading="lazy">
                        <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark bg-opacity-75 border border-secondary text-white fs-xs">
                            Ep. {{ $currentEp }} / {{ $totalEp }}
                        </span>
                    </div>
                    <div class="card-body p-2 text-center bg-dark bg-opacity-75">
                        <h6 class="fw-bold mb-1 text-truncate fs-6" title="{{ $title }}">
                            {{ $title }}
                        </h6>
                        <small class="text-primary d-block text-truncate fw-semibold fs-xs">
                            In visione
                        </small>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="p-4 text-center border border-secondary border-dashed rounded-3 text-muted">
                    <div class="fs-2 mb-2">📺</div>
                    <p class="mb-0 fw-semibold">Nessun anime in corso al momento.</p>
                    <small class="text-muted">Aggiungi i titoli che stai guardando dalla tua libreria!</small>
                </div>
            </div>
            @endforelse
        </div>
        @endif

        <!-- CONTENUTO TAB 2: STATISTICHE -->
        @if($activeTab === 'stats')
        <h5 class="fw-bold mb-3">📊 Statistiche di Visione</h5>
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="p-3 bg-secondary bg-opacity-25 rounded-3 border border-secondary border-opacity-25 text-center">
                    <div class="fs-3 fw-bold text-primary">{{ $stats['total_completed'] ?? 0 }}</div>
                    <div class="text-muted small">Anime Completati</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-secondary bg-opacity-25 rounded-3 border border-secondary border-opacity-25 text-center">
                    <div class="fs-3 fw-bold text-success">{{ $stats['episodes_watched'] ?? 0 }}</div>
                    <div class="text-muted small">Episodi Visti</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-secondary bg-opacity-25 rounded-3 border border-secondary border-opacity-25 text-center">
                    <div class="fs-3 fw-bold text-warning">{{ $stats['time_watched_hours'] ?? 0 }}h</div>
                    <div class="text-muted small">Tempo Totale</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-3 bg-secondary bg-opacity-25 rounded-3 border border-secondary border-opacity-25 text-center">
                    <div class="fs-3 fw-bold text-info">{{ $stats['favorite_genre'] ?? 'N/D' }}</div>
                    <div class="text-muted small">Genere Preferito</div>
                </div>
            </div>
        </div>
        @endif

        <!-- CONTENUTO TAB 3: TROFEI & BADGE -->
        @if($activeTab === 'badges')
        <h5 class="fw-bold mb-3 text-white">🏆 Bacheca Badge Sbloccati</h5>
        <div class="row g-3">
            @forelse($badges ?? [] as $badge)
            <div class="col-md-6">
                <div class="d-flex align-items-center p-3 rounded-3 border {{ $badge['unlocked'] ? 'bg-dark bg-gradient border-warning shadow-sm' : 'bg-dark border-secondary opacity-50' }}">
                    <div class="fs-1 me-3 align-self-center">{{ $badge['icon'] }}</div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <h6 class="fw-bold mb-0 {{ $badge['unlocked'] ? 'text-warning' : 'text-secondary' }}">
                                {{ $badge['title'] }}
                            </h6>
                            @if(!$badge['unlocked'])
                            <span class="badge bg-secondary text-white fs-xs">🔒 Bloccato</span>
                            @else
                            <span class="badge bg-warning text-dark fw-bold fs-xs">✨ Sbloccato</span>
                            @endif
                        </div>
                        <p class="small mb-0 {{ $badge['unlocked'] ? 'text-light-50' : 'text-secondary' }}">
                            {{ $badge['description'] }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">
                Nessun badge disponibile al momento.
            </div>
            @endforelse
        </div>
        @endif
    </div>
</div>

@livewire('profile.favorite-selector')
</div>