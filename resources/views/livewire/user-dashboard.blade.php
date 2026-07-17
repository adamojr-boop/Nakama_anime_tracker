<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">こんにちは, {{ Auth::user()->name }}! </h1>
            <p class="text-muted">Bentornato nella tua area Nakama. Ecco la tua lista anime.</p>
        </div>
    </div>
    <!-- Menu Filtri (Tab di Bootstrap) -->
    <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3 shadow-sm" style="max-width: max-content;">
        <li class="nav-item">
            <button wire:click="setFilter('watching')" class="nav-item nav-link fw-semibold px-4 py-2 border-0 rounded-3 {{ $currentFilter === 'watching' ? 'active btn-primary' : 'text-secondary' }}">
                📺 In Corso
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="setFilter('completed')" class="nav-item nav-link fw-semibold px-4 py-2 border-0 rounded-3 {{ $currentFilter === 'completed' ? 'active btn-primary' : 'text-secondary' }}">
                🎉 Completati
            </button>
        </li>
    </ul>
    <!-- Indicatore di caricamento Livewire -->
    <div wire:loading class="text-center w-100 my-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Caricamento...</span>
        </div>
    </div>
    <!-- Griglia degli Anime -->
    <div wire:loading.remove class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @forelse($animeList as $anime)
        <div class="col">
            <div class="card h-100 shadow-sm border-0 bg-white overflow-hidden text-decoration-none">
                <a href="{{ route('anime.show', $anime['mal_id']) }}" class="position-relative d-block">
                    <img src="{{ $anime['image'] }}" class="card-img-top" alt="{{ $anime['title'] }}" style="height: 280px; object-fit: cover;">
                </a>
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="card-title fw-bold text-dark mb-2 text-truncate-2" style="min-height: 44px; font-size: 0.95rem;">
                            <a href="{{ route('anime.show', $anime['mal_id']) }}" class="text-decoration-none text-dark">{{ $anime['title'] }}</a>
                        </h6>
                    </div>

                    <div class="mt-2">
                        <div class="d-flex justify-content-between text-muted small mb-1fw-semibold">
                            <span>Episodi visti</span>
                            <span class="fw-bold text-primary">{{ $anime['watched_episodes'] }} / {{ $anime['total_episodes'] }}</span>
                        </div>

                        <!-- Calcolo dinamico della barra di progresso -->
                        @php
                        $total = is_numeric($anime['total_episodes']) ? (int)$anime['total_episodes'] : 0;
                        $percentage = $total > 0 ? ($anime['watched_episodes'] / $total) * 100 : 0;
                        @endphp
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $currentFilter === 'completed' ? 'bg-success' : 'bg-primary' }}"
                                role="progressbar"
                                style="width: {{ $percentage }}%;"
                                aria-valuenow="{{ $percentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 w-100 text-center py-5">
            <div class="p-4 bg-light border rounded-3">
                <p class="text-muted mb-0">Nessun anime trovato in questa sezione. Vai ad esplorare qualche titolo!</p>
            </div>
        </div>
        @endforelse
    </div>
</div>