<div>
    <div class="row">
        <!-- COLONNA CREAZIONE LISTA -->
        <div class="col-md-4 mb-4">
            <div class="card card-body shadow-sm border-0 bg-white">
                <h5 class="fw-bold mb-3 text-secondary">✨ Nuova Lista Tematica</h5>

                @if (session()->has('message'))
                <div class="alert alert-success py-2 small">{{ session('message') }}</div>
                @endif

                <form wire:submit.prevent="createList">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nome Lista</label>
                        <input type="text" wire:model="name" class="form-control form-control-sm" placeholder="Es. Miglior Plot Twist, Comfort Movies">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Descrizione (Opzionale)</label>
                        <textarea wire:model="description" class="form-control form-control-sm" rows="2" placeholder="Di cosa tratta questa lista?"></textarea>
                        @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" wire:model="is_public" id="flexSwitchCheckDefault">
                        <label class="form-check-input-label small fw-semibold text-secondary" for="flexSwitchCheckDefault">
                            🌐 Rendi la lista pubblica
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">Crea Lista</button>
                </form>
            </div>
        </div>
        <!-- COLONNA ELENCO LISTE -->
        <div class="col-md-8">
            <h5 class="fw-bold mb-3 text-secondary">📂 Le Tue Liste Definitive</h5>

            <div class="row g-3">
                @foreach($lists as $list)
                @php $isCurrent = $selectedListId === $list->id; @endphp
                <div class="col-12">
                    <!-- Card Cliccabile grazie a wire:click="selectList" e cursore pointer -->
                    <div class="card shadow-sm border-0 bg-white p-3 cursor-pointer transition-all {{ $isCurrent ? 'border-start border-primary border-4 shadow' : '' }}"
                        wire:click="selectList({{ $list->id }})"
                        style="cursor: pointer;">

                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-100">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="fw-bold mb-0 text-dark">
                                        {{ $list->type === 'wishlist' ? '📌 ' . $list->name : '📁 ' . $list->name }}
                                    </h5>
                                    <!-- Ferma la propagazione del clic sul badge privacy per non attivare l'espansione della card -->
                                    <div wire:click.stop>
                                        @if($list->type === 'wishlist')
                                        <span class="badge bg-light text-secondary border small">Sistema</span>
                                        @else
                                        <button wire:click="togglePrivacy({{ $list->id }})" class="badge border-0 {{ $list->is_public ? 'bg-success text-white' : 'bg-secondary text-white' }}">
                                            {{ $list->is_public ? '🌐 Pubblica' : '🔒 Privata' }}
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-muted small mb-2">{{ $list->description ?? 'Nessuna descrizione.' }}</p>
                                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                                    <span class="text-primary small fw-semibold">
                                        {{ is_array($list->anime_ids) ? count($list->anime_ids) : 0 }} anime salvati
                                        <span class="text-muted text-xs ms-1">({{ $isCurrent ? 'Clicca per chiudere' : 'Clicca per visualizzare' }})</span>
                                    </span>
                                    <div class="d-flex align-items-center gap-2" wire:click.stop>
                                        <a href="{{ route('lists.show', $list->id) }}"
                                            class="btn btn-outline-primary btn-sm fw-semibold text-decoration-none">
                                            Apri dettaglio lista
                                        </a>
                                        <!-- Bottone Cancella (Ferma propagazione) -->
                                        @if($list->type !== 'wishlist')
                                        <button wire:click="deleteList({{ $list->id }})" class="btn btn-outline-danger btn-sm border-0 p-1" onclick="confirm('Vuoi davvero eliminare questa lista?') || event.stopImmediatePropagation()">
                                            🗑️
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 🌟 CONTENUTO ESPANSO: Mostra gli anime dentro la lista selezionata -->
                        @if($isCurrent)
                        <div class="mt-3 pt-3 border-top" wire:click.stop>
                            <div class="d-flex flex-wrap gap-3">
                                @forelse($selectedListAnime as $anime)
                                <!-- Piccola card compatta per ogni anime salvato -->
                                <div class="card border-0 bg-light p-2 text-center shadow-sm position-relative" style="width: 120px;">
                                    <a href="{{ route('anime.show', $anime['mal_id']) }}" class="text-decoration-none text-dark d-block h-100">
                                        <img src="{{ $anime['image'] }}" class="img-fluid rounded mb-2 shadow-sm" style="height: 140px; object-fit: cover; width: 100%;">
                                        <h6 class="fw-bold small text-truncate mb-0" style="font-size: 0.8rem;" title="{{ $anime['title'] }}">
                                            {{ $anime['title'] }}
                                        </h6>
                                    </a>
                                </div>
                                @empty
                                <div class="text-muted small py-2 w-100">
                                    💨 Questa lista è ancora vuota. Vai nella scheda di un anime per aggiungerlo!
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>