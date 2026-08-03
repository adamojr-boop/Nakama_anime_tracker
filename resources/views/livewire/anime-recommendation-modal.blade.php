<div>
    <!-- Pulsante da inserire nella Navbar -->
    <button type="button" class="btn btn-outline-warning btn-sm fw-bold d-flex align-items-center gap-1 rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#timeRecommendationModal">
        🎲 Quanto tempo hai?
    </button>

    @teleport('body')
    <!-- Modal Bootstrap -->
    <div wire:ignore.self class="modal fade" id="timeRecommendationModal" tabindex="-1" aria-labelledby="timeRecommendationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary shadow-lg rounded-4">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold text-warning d-flex align-items-center gap-2" id="timeRecommendationModalLabel">
                        ⏱️ Consiglio Lampo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Form controlli tempo -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">QUANTO TEMPO HAI ORA? (MINUTI)</label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range flex-grow-1" min="15" max="180" step="15" wire:model.live="availableTime">
                            <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill" style="min-width: 80px;">
                                {{ $availableTime }} min
                            </span>
                        </div>
                        <div class="d-flex justify-content-between text-muted fs-xs mt-1">
                            <span>15m (Pausa)</span>
                            <span>45m (2 Ep)</span>
                            <span>90m (Film/4 Ep)</span>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="includePlanToWatch" wire:model.live="includePlanToWatch">
                        <label class="form-check-label small text-light" for="includePlanToWatch">
                            Includi anche gli anime da <strong>"Da Guardare"</strong>
                        </label>
                    </div>

                    <button wire:click="generateRecommendation" class="btn btn-warning w-100 fw-bold py-2 rounded-3 shadow">
                        🎯 Genera Consiglio Casual
                    </button>

                    <!-- RISULTATO RECOMMENDATION -->
                    @if($recommendation)
                    <div class="mt-4 p-3 bg-secondary bg-opacity-25 border border-warning rounded-3 animate__animated animate__fadeIn">
                        <div class="d-flex gap-3 align-items-center">

                            <!-- IMMAGINE CLICCABILE -->
                            @if(!empty($recommendation['image_url']))
                            <a href="https://myanimelist.net/anime/{{ $recommendation['mal_id'] }}" target="_blank" title="Vedi su MyAnimeList">
                                <img src="{{ $recommendation['image_url'] }}"
                                    class="rounded object-fit-cover shadow-sm hover-scale"
                                    style="width: 70px; height: 100px; transition: transform 0.2s;"
                                    alt="{{ $recommendation['title'] }}">
                            </a>
                            @endif

                            <div>
                                <span class="badge bg-success mb-1">
                                    {{ $recommendation['status'] === 'watching' ? 'In Corso' : 'Da Guardare' }}
                                </span>

                                <!-- TITOLO CLICCABILE -->
                                <h6 class="fw-bold mb-1">
                                    <a href="https://myanimelist.net/anime/{{ $recommendation['mal_id'] }}"
                                        target="_blank"
                                        class="text-white text-decoration-none hover-warning">
                                        {{ $recommendation['title'] }} ↗
                                    </a>
                                </h6>

                                <p class="small text-light mb-1">
                                    👉 Ti consigliamo di guardare
                                    <strong class="text-warning">
                                        @if($recommendation['episodes_to_watch'] == 1)
                                        l'Episodio {{ $recommendation['start_episode'] }}
                                        @else
                                        gli Episodi da {{ $recommendation['start_episode'] }} a {{ $recommendation['end_episode'] }}
                                        @endif
                                    </strong>
                                </p>

                                <small class="text-muted d-block fs-xs">
                                    ⏱️ Tempo stimato: ~{{ $recommendation['total_time_needed'] }} min
                                </small>
                            </div>
                        </div>

                        <!-- MOSTRA IL COMPONENTE (Gestirà lui se mostrare i link o l'avviso "Non trovato") -->
                        <div class="mt-3 pt-2 border-top border-secondary border-opacity-50">
                            <x-streaming-providers :platforms="$recommendation['streaming_platforms'] ?? []" />
                        </div>
                    </div>
                    @endif

                    <!-- ERRORE / FALLBACK -->
                    @if($errorMessage)
                    <div class="mt-4 alert alert-warning border-0 bg-warning bg-opacity-10 text-warning small mb-0 rounded-3">
                        {{ $errorMessage }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endteleport
</div>