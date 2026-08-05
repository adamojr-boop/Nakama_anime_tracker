<div>
    @if($showModal)
    <!-- Overlay Scuro -->
    <div class="modal-backdrop fade show" wire:click="closeModal" style="z-index: 1050;"></div>

    <!-- Modale Mobile Friendly -->
    <div class="modal d-block fade show" tabindex="-1" style="z-index: 1055;">
        <div class="modal-dialog modal-dialog-centered px-2">
            <div class="modal-content bg-dark text-white border-secondary shadow-lg">

                <div class="modal-header border-secondary p-3">
                    <h6 class="modal-title fw-bold">⭐ Vetrina Preferiti</h6>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>

                <div class="modal-body p-3">
                    <!-- Selezione Slot -->
                    <label class="form-label small text-muted mb-2">1. Seleziona lo Slot (1-5):</label>
                    <div class="d-flex gap-1 mb-3">
                        @for($s = 1; $s <= 5; $s++)
                            <button
                            type="button"
                            wire:click="selectSlot({{ $s }})"
                            class="btn btn-sm flex-fill fw-bold {{ $selectedSlot === $s ? 'btn-danger' : 'btn-outline-secondary' }}">
                            Slot {{ $s }}
                            </button>
                            @endfor
                    </div>

                    <!-- Barra di Ricerca -->
                    <label class="form-label small text-muted mb-1">2. Cerca l'Anime da assegnare allo Slot {{ $selectedSlot }}:</label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control form-control-sm bg-secondary bg-opacity-25 border-secondary text-white mb-3"
                        placeholder="Scrivi il titolo dell'anime...">

                    <!-- Risultati della Ricerca -->
                    <div class="list-group list-group-flush border-top border-secondary pt-2" style="max-height: 220px; overflow-y: auto;">
                        @forelse($animes as $anime)
                        <button
                            type="button"
                            wire:click="assignAnimeToSlot({{ $anime->id }})"
                            class="list-group-item list-group-item-action bg-dark text-white border-secondary d-flex align-items-center gap-2 p-2">
                            <img src="{{ $anime->image_url ?? $anime->image }}" class="rounded object-fit-cover" style="width: 35px; height: 50px;">
                            <div class="text-start text-truncate flex-grow-1">
                                <div class="fw-bold small text-truncate">{{ $anime->title }}</div>
                                <span class="text-danger fs-xs">Assegna a Slot {{ $selectedSlot }}</span>
                            </div>
                        </button>
                        @empty
                        <div class="text-center text-muted py-3 small">
                            Nessun anime trovato.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="modal-footer border-secondary p-2">
                    <button type="button" wire:click="closeModal" class="btn btn-sm btn-secondary w-100">Chiudi</button>
                </div>

            </div>
        </div>
    </div>
    @endif
</div>