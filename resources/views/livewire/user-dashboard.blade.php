<div class="container mt-4">
    <!-- Header di benvenuto -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">こんにちは, {{ auth()->user()->name }}! </h1>
            <p class="text-muted mb-0">Bentornato nella tua area Nakama. Ecco la tua lista anime.</p>
        </div>
    </div>
    <!-- Widgets Livewire -->
    <div class="mb-4">
        <livewire:user-stats-widget />
        <div class="card p-3 shadow-sm border-0 bg-dark text-white">
            <div class="d-flex align-items-center">
                <div class="fs-1 me-3">🍿</div>
                <div>
                    <h6 class="mb-0 text-muted">Record Maratona</h6>
                    <h4 class="fw-bold mb-0">{{ $maxBinge }} Episodi Consecutivi</h4>
                </div>
            </div>
        </div>
    </div>
    <livewire:user-badges-widget />
    <livewire:quiz-hub />
    <!-- Classifica Community -->
    <div class="col-lg-4">
        <h3 class="fw-bold mb-3">🏆 Top Giocatori</h3>
        <div class="card bg-dark text-white border-secondary p-3 shadow-sm">
            <ul class="list-group list-group-flush bg-transparent">
                @foreach($leaderboard as $index => $entry)
                <li class="list-group-flush list-group-item bg-transparent text-white d-flex justify-content-between align-items-center border-secondary px-0">
                    <div>
                        <span class="fw-bold me-2">#{{ $index + 1 }}</span>
                        {{ $entry->user->name ?? 'Utente' }}
                    </div>
                    <span class="badge bg-warning text-dark fs-6">{{ number_format($entry->total_score) }} PTS</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
<!-- Menu Filtri -->
<ul class="nav nav-pills mb-4 bg-light p-2 rounded-3 shadow-sm d-flex flex-wrap gap-1">
    <li class="nav-item">
        <button wire:click="setFilter('watching')" class="nav-link fw-semibold {{ $currentFilter === 'watching' ? 'active bg-primary text-white' : 'text-secondary' }}">📺 In Corso</button>
    </li>
    <li class="nav-item">
        <button wire:click="setFilter('plan_to_watch')" class="nav-link fw-semibold {{ $currentFilter === 'plan_to_watch' ? 'active bg-primary text-white' : 'text-secondary' }}">⏳ Da Guardare</button>
    </li>
    <li class="nav-item">
        <button wire:click="setFilter('completed')" class="nav-link fw-semibold {{ $currentFilter === 'completed' ? 'active bg-primary text-white' : 'text-secondary' }}">🎉 Completati</button>
    </li>
    <li class="nav-item">
        <button wire:click="setFilter('dropped')" class="nav-link fw-semibold {{ $currentFilter === 'dropped' ? 'active bg-primary text-white' : 'text-secondary' }}">❌ Abbandonati</button>
    </li>
    <li class="nav-item">
        <button wire:click="setFilter('my-lists')" class="nav-link fw-semibold {{ $currentFilter === 'my-lists' ? 'active bg-primary text-white' : 'text-secondary' }}">📂 Le mie Liste</button>
    </li>
</ul>
<!-- Contenuto della Tab Corrente -->
@if($currentFilter === 'my-lists')
<livewire:manage-custom-lists />
@else
<div class="row g-3">
    @forelse($animeList as $anime)
    <div class="col-md-3 col-sm-6" wire:key="anime-{{ $anime['mal_id'] }}">
        <div class="card h-100 shadow-sm border-0 position-relative">
            <a href="{{ route('anime.show', $anime['mal_id']) }}" class="text-decoration-none text-dark">
                <img src="{{ $anime['image'] }}" class="card-img-top" style="height: 250px; object-fit: cover;">

                <div class="card-body p-2 text-center">
                    <h6 class="fw-bold text-truncate mb-1">{{ $anime['title'] }}</h6>

                    @php
                    $total = (int) ($anime['total_episodes'] ?? 0);
                    $watched = (int) ($anime['watched_episodes'] ?? 0);
                    $duration = (int) ($anime['episode_duration'] ?? 24); // Se non c'è episode_duration usa 24

                    $remainingEp = $total > 0 ? max(0, $total - $watched) : 0;
                    $remainingMinutes = $remainingEp * $duration;
                    $remHours = floor($remainingMinutes / 60);
                    $remMins = $remainingMinutes % 60;
                    $percent = $total > 0 ? round(($watched / $total) * 100) : 0;
                    @endphp

                    <div class="d-flex justify-content-between align-items-center small mb-1">
                        <span class="badge bg-secondary">Ep. {{ $watched }} / {{ $total > 0 ? $total : '?' }}</span>
                        <span class="fw-semibold text-primary" style="font-size: 0.75rem;">{{ $percent }}%</span>
                    </div>
                    <!-- Progress bar completamento -->
                    <div class="progress mb-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percent }}%;"></div>
                    </div>
                    <!-- Indicatore dinamico del tempo rimanente -->
                    @if($currentFilter === 'watching' && $remainingEp > 0)
                    <div class="text-muted text-xs" style="font-size: 0.75rem;">
                        ⏳ Rimangono: <strong>{{ $remHours > 0 ? $remHours.'h ' : '' }}{{ $remMins }}m</strong>
                    </div>
                    @elseif($currentFilter === 'completed')
                    <div class="text-success text-xs fw-semibold" style="font-size: 0.75rem;">
                        🎉 Serie Completata!
                    </div>
                    @endif
                </div>
            </a>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-4">
        <p class="text-muted">💨 Nessun anime trovato in questa categoria.</p>
    </div>
    @endforelse
</div>
@endif
</div>