<div class="position-relative w-100 m-auto" style="max-width: 500px;">
    <!-- Input di ricerca -->
    <div class="input-group">
        <span class="input-group-text bg-white border-end-0">🔍</span>
        <input
            wire:model.live.debounce.300ms="searchQuery"
            type="text"
            class="form-control border-start-0"
            placeholder="Cerca un anime...">
    </div>
    <!-- Caricamento visivo asincrono -->
    <div wire:loading class="position-absolute w-100 bg-white shadow-sm p-2 text-center text-muted" style="z-index: 1000; top: 100%;">
        <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Cercando su Nakama...
    </div>
    <!-- Lista dei Risultati (Mostrata solo se ci sono elementi nell'array) -->
    @if(!empty($results))
    <div class="list-group position-absolute w-100 shadow-lg mt-1" style="z-index: 1000; top: 100%; max-height: 350px; overflow-y: auto;">
        @foreach($results as $anime)
        <a href="{{ route('anime.show', $anime['mal_id']) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-2">
            <!-- Locandina -->
            <img src="{{ $anime['images']['jpg']['small_image_url'] }}"
                alt="{{ $anime['title'] }}"
                class="rounded"
                style="width: 40px; height: 55px; object-fit: cover;">
            <!-- Info Anime -->
            <div class="flex-grow-1">
                <h6 class="mb-0 text-truncate font-weight-bold" style="max-width: 280px;">{{ $anime['title'] }}</h6>
                <small class="text-muted">
                    {{ $anime['type'] ?? 'TV' }} • {{ $anime['episodes'] ?? '?' }} Ep.
                </small>
            </div>
            <span class="badge bg-primary rounded-pill">★ {{ $anime['score'] ?? 'N/D' }}</span>
        </a>
        @endforeach
    </div>
    @endif

    @if(empty($results) && strlen($searchQuery) >= 3)
    <div wire:loading.remove class="position-absolute w-100 bg-white shadow-sm p-3 text-center text-muted mt-1" style="z-index: 1000; top: 100%;">
        Nessun risultato trovato o rallentamento del server. Riprova tra un istante.
    </div>
    @endif
</div>