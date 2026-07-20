<div id="anime-tracker" class="card card-body shadow-sm mb-4 bg-light border-0">
    @auth

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-secondary">🎬 Selezione Episodi Visti</h5>
        <div class="d-flex align-items-center gap-2">
            @if($rewatchCount > 0)
            <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill shadow-sm" title="Quante volte hai rivisto questo anime">
                🔄 Rewatch #{{ $rewatchCount }}
            </span>
            @endif
            <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">
                {{ count($watchedEpisodesList) }} / {{ $totalEpisodes > 0 ? $totalEpisodes : '?' }} Visti
            </span>
        </div>
    </div>
    <!-- DROPDOWN PER CAMBIARE STATO -->
    <div class="d-flex align-items-center justify-content-between mb-3 bg-white p-2 rounded shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <span class="small fw-bold text-muted text-uppercase ms-1">Stato Visione:</span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($currentStatus === 'watching') 📺 In Corso
                    @elseif($currentStatus === 'plan_to_watch') ⏳ Da Guardare
                    @elseif($currentStatus === 'completed') 🎉 Completato
                    @elseif($currentStatus === 'dropped') ❌ Abbandonato
                    @else ⚪ Non Tracciato
                    @endif
                </button>
                <ul class="dropdown-menu shadow-sm">
                    <li>
                        <button wire:click="changeStatus('watching')" class="dropdown-item small d-flex align-items-center gap-2 {{ $currentStatus === 'watching' ? 'active' : '' }}">
                            📺 In Corso
                        </button>
                    </li>
                    <li>
                        <button wire:click="changeStatus('plan_to_watch')" class="dropdown-item small d-flex align-items-center gap-2 {{ $currentStatus === 'plan_to_watch' ? 'active' : '' }}">
                            ⏳ Da Guardare
                        </button>
                    </li>
                    <li>
                        <button wire:click="changeStatus('completed')" class="dropdown-item small d-flex align-items-center gap-2 {{ $currentStatus === 'completed' ? 'active' : '' }}">
                            🎉 Completato
                        </button>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <button wire:click="changeStatus('dropped')" class="dropdown-item small text-danger d-flex align-items-center gap-2 {{ $currentStatus === 'dropped' ? 'bg-danger text-white' : '' }}">
                            ❌ Abbandona Serie
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <!-- 🌟 Pulsante per iniziare il Rewatch se l'anime è completato -->
        @if($currentStatus === 'completed')
        <button wire:click="startRewatch"
            wire:confirm="Vuoi davvero ricominciare questo anime? Gli episodi verranno resettati ma il tuo contatore Rewatch aumenterà!"
            class="btn btn-sm btn-outline-warning text-dark fw-bold d-flex align-items-center gap-1 shadow-sm">
            🔄 Ricomincia Anime (Rewatch)
        </button>
        @endif
    </div>
    <!-- Griglia Episodi -->
    <div class="d-flex flex-wrap gap-2 overflow-auto p-1" style="max-height: 250px;">
        @if($totalEpisodes > 0)
        @for ($i = 1; $i <= $totalEpisodes; $i++)
            @php
            $isWatched=in_array($i, $watchedEpisodesList);
            @endphp
            <button
            wire:click="toggleEpisode({{ $i }})"
            class="btn btn-sm text-center fw-semibold d-flex align-items-center justify-content-center shadow-sm transition-all"
            style="width: 42px; height: 42px; min-width: 42px; font-size: 0.85rem; border-radius: 8px; 
                           {{ $isWatched ? 'background-color: #0d6efd; color: white; border: none;' : 'background-color: #ffffff; color: #495057; border: 1px solid #dee2e6;' }}"
            title="{{ $isWatched ? 'Segna come non visto' : 'Segna come visto' }}">
            {{ $i }}
            </button>
            @endfor
            @else
            @foreach($watchedEpisodesList as $ep)
            <button
                wire:click="toggleEpisode({{ $ep }})"
                class="btn btn-sm btn-primary text-center fw-semibold d-flex align-items-center justify-content-center shadow-sm"
                style="width: 42px; height: 42px; min-width: 42px; border-radius: 8px;">
                {{ $ep }}
            </button>
            @endforeach

            @php
            $nextSuggested = empty($watchedEpisodesList) ? 1 : max($watchedEpisodesList) + 1;
            @endphp
            <button
                wire:click="toggleEpisode({{ $nextSuggested }})"
                class="btn btn-sm btn-outline-secondary border-dashed text-center fw-semibold d-flex align-items-center justify-content-center shadow-sm"
                style="width: 42px; height: 42px; min-width: 42px; border-radius: 8px; border-style: dashed;">
                +{{ $nextSuggested }}
            </button>
            @endif
    </div>
    <!-- Badge celebrativo se completato -->
    @if($totalEpisodes > 0 && count($watchedEpisodesList) === $totalEpisodes)
    <div class="mt-3 alert alert-success py-2 text-center mb-0 border-0 fw-semibold text-success small animate__animated animate__fadeIn">
        🎉 Complimenti! Hai completato ogni singolo episodio di questo anime!
    </div>
    @endif

    @else
    <div class="text-center py-2">
        <p class="text-muted mb-2 small">Vuoi gestire il tracciamento avanzato degli episodi?</p>
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Accedi per tracciare</a>
    </div>
    @endauth
</div>