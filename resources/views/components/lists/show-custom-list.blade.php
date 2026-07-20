<x-layouts.app>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <h1 class="fw-bold mb-1">
                    {{ $list->type === 'wishlist' ? '📌 ' . $list->name : '📁 ' . $list->name }}
                </h1>
                <p class="text-muted mb-0">
                    {{ $list->description ?? 'Nessuna descrizione.' }}
                </p>
            </div>

            <div class="text-end">
                <span class="badge {{ $list->is_public ? 'bg-success' : 'bg-secondary' }} mb-2">
                    {{ $list->is_public ? 'Lista pubblica' : 'Lista privata' }}
                </span>
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        Torna alla dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Anime nella lista</h5>
                    <span class="text-muted small">{{ count($animeList) }} elementi</span>
                </div>

                @if(empty($animeList))
                <div class="text-muted py-4 text-center">
                    Questa lista non contiene ancora anime.
                </div>
                @else
                <div class="row g-3">
                    @foreach($animeList as $anime)
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="card border-0 bg-light h-100 shadow-sm">
                            <img src="{{ $anime['image'] }}" class="card-img-top" style="height: 220px; object-fit: cover;">
                            <div class="card-body p-2 text-center d-flex flex-column gap-2">
                                <h6 class="small fw-bold text-truncate mb-0" title="{{ $anime['title'] }}">
                                    {{ $anime['title'] }}
                                </h6>

                                <div class="d-grid gap-1">
                                    <a href="{{ route('anime.show', $anime['mal_id']) }}" class="btn btn-outline-secondary btn-sm">
                                        Apri dettaglio
                                    </a>

                                    @if(($anime['status'] ?? null) === 'completed')
                                    <a href="{{ route('anime.show', $anime['mal_id']) }}#anime-tracker" class="btn btn-outline-warning btn-sm fw-semibold">
                                        🔄 Ricomincia Anime (Rewatch)
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>