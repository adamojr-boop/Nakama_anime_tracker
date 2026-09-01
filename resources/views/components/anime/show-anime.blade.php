<div>
    <div class="container mt-4">
        @if($anime)
        <div class="row">
            <!-- Colonna Sinistra: Locandina e Azioni Rapide -->
            <div class="col-md-4 text-center mb-4">
                <img src="{{ $anime['images']['jpg']['large_image_url'] ?? 'https://via.placeholder.com/225x320' }}"
                    alt="{{ $anime['title'] }}"
                    class="img-fluid rounded shadow mb-3"
                    style="max-width: 100%; height: auto;">
                <!-- 🌟 Il bottone delle liste ora è posizionato correttamente sotto la foto -->
                <div class="mb-3">
                    <livewire:add-to-list-button :malId="$anime['mal_id']" />
                </div>
            </div>
            <!-- Colonna Destra: Dettagli Anime -->
            <div class="col-md-8">
                <h1 class="mb-2">{{ $anime['title'] }}</h1>

                <div class="mb-3">
                    <span class="badge bg-primary">★ {{ $anime['score'] ?? 'N/D' }}</span>
                    <span class="badge bg-secondary">{{ $anime['type'] ?? 'TV' }}</span>
                    <span class="badge bg-dark">{{ $anime['episodes'] ?? '?' }} Ep.</span>
                </div>

                <livewire:anime-tracker :malId="$anime['mal_id']" :totalEpisodes="$anime['episodes']" />

                <h5 class="mt-4">Sinossi</h5>
                <p class="text-muted" style="line-height: 1.6;">
                    {{ $anime['synopsis'] ?? 'Nessuna trama disponibile per questo anime.' }}
                </p>

                <livewire:anime-streaming-providers
                    :mal-id="$anime['mal_id']"
                    :title="$anime['title']" />
            </div>
        </div>

        <!-- Valutazione + Discussione Episodio (Collassabile) -->
        <div class="mt-4" x-data="{ openEpisodePanel: true }">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="mb-0 fw-bold">Valutazione e Discussione Episodio</h5>
                <button type="button"
                    class="btn btn-sm btn-outline-secondary"
                    @click="openEpisodePanel = !openEpisodePanel">
                    <span x-text="openEpisodePanel ? 'Chiudi' : 'Apri'"></span>
                </button>
            </div>

            <div x-show="openEpisodePanel" x-transition>
                <livewire:episodes.rate-episode
                    :anime-mal-id="data_get($anime, 'mal_id')"
                    :episode-number="1" />
            </div>
        </div>

        <!-- Sezione Recensioni -->
        <div class="row mt-4">
            <div class="col-12">
                <livewire:anime-reviews :animeId="$anime['mal_id']" />
            </div>
        </div>
        @else
        <div class="alert alert-warning text-center">
            Impossibile caricare i dati dell'anime. Riprova più tardi.
        </div>
        @endif
    </div>
</div>