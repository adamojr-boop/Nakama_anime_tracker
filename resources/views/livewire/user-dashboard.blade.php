<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">こんにちは, {{ Auth::user()->name }}! </h1>
            <p class="text-muted">Bentornato nella tua area Nakama. Ecco la tua lista anime.</p>
        </div>
    </div>
    <div>
        <div class="container mt-4">
            <!-- Menu Filtri (Assicurati che i valori siano esattamente questi) -->
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
                    <div class="card h-100 shadow-sm border-0">
                        <img src="{{ $anime['image'] }}" class="card-img-top" style="height: 250px; object-fit: cover;">
                        <div class="card-body p-2 text-center">
                            <h6 class="fw-bold text-truncate mb-1">{{ $anime['title'] }}</h6>
                            <span class="badge bg-secondary small">Ep. {{ $anime['watched_episodes'] }} / {{ $anime['total_episodes'] }}</span>
                        </div>
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
    </div>
</div>