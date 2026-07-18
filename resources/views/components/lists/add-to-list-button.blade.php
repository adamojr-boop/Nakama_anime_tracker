<div>
    @auth
    <div class="dropdown">
        <button class="btn btn-outline-secondary w-100 dropdown-toggle fw-semibold d-flex align-items-center justify-content-center gap-2"
            type="button"
            id="dropdownMenuButton"
            data-bs-toggle="dropdown"
            aria-expanded="false">
            📁 Aggiungi alla lista...
        </button>
        <!-- 🌟 Trasformato in DIV (rimuove i pallini ed è HTML valido al 100%) -->
        <div class="dropdown-menu dropdown-menu-end shadow w-100 p-2" aria-labelledby="dropdownMenuButton" style="min-width: 240px;">
            <span class="dropdown-header small text-uppercase fw-bold text-muted px-2">Le tue Collezioni</span>

            <div class="overflow-auto" style="max-height: 200px;">
                @forelse($myLists as $list)
                @php
                $inList = is_array($list->anime_ids) && in_array($malId, $list->anime_ids);
                @endphp
                <button wire:click="toggleAnimeInList({{ $list->id }})"
                    class="dropdown-item d-flex justify-content-between align-items-center py-2 rounded">
                    <span class="text-truncate" style="max-width: 180px;">
                        {{ $list->type === 'wishlist' ? '📌' : '📁' }} {{ $list->name }}
                    </span>
                    @if($inList)
                    <span class="text-success fw-bold">✓</span>
                    @endif
                </button>
                @empty
                <span class="dropdown-item-text small text-muted px-2">Nessuna lista.</span>
                @endforelse
            </div>

            <hr class="dropdown-divider">
            <!-- MINI-FORM CREAZIONE RAPIDA (Ora dentro un comodo div) -->
            <div class="px-2 py-1" wire:click.stop>
                <label class="form-label small fw-bold text-muted mb-1">Crea nuova lista:</label>
                <div class="input-group input-group-sm">
                    <input type="text"
                        wire:model="newListName"
                        wire:keydown.enter="createQuickList"
                        class="form-control"
                        placeholder="Es. Comfort Movies">
                    <button wire:click="createQuickList" class="btn btn-primary fw-bold" type="button">+</button>
                </div>
            </div>

            <hr class="dropdown-divider">

            <a href="{{ route('dashboard') }}" class="dropdown-item small text-center text-secondary fw-semibold">
                ⚙️ Vai al Pannello Gestionale
            </a>
        </div>
    </div>
    @else
    <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 fw-semibold">
        🔒 Accedi per salvare in lista
    </a>
    @endauth
</div>