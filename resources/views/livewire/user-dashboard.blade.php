<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">こんにちは, {{ auth()->user()->name }}! </h1>
            <p class="text-muted">Bentornato nella tua area Nakama. Ecco la tua lista anime.</p>
        </div>
    </div>
    <div>
        <div class="container mt-4">

            <livewire:user-stats-widget />
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
                    <div class="card h-100 shadow-sm border-0 position-relative">
                        <a href="{{ route('anime.show', $anime['mal_id']) }}" class="text-decoration-none text-dark">
                            <img src="{{ $anime['image'] }}" class="card-img-top" style="height: 250px; object-fit: cover;">

                            <div class="card-body p-2 text-center">
                                <h6 class="fw-bold text-truncate mb-1">{{ $anime['title'] }}</h6>

                                @php
                                $total = $anime['total_episodes'] ?? 0;
                                $watched = $anime['watched_episodes'] ?? 0;
                                $duration = $anime['episode_duration'] ?? 24;
                                $remainingEp = $total > 0 ? max(0, $total - $watched) : 0;
                                $remainingMinutes = $remainingEp * $duration;
                                $remHours = floor($remainingMinutes / 60);
                                $remMins = $remainingMinutes % 60;
                                $percent = $total > 0 ? round(($watched / $total) * 100) : 0;
                                @endphp

                                <div class="d-flex justify-content-between align-items-center small mb-1">
                                    <span class="badge bg-secondary">Ep. {{ $watched }} / {{ $total > 0 ? $total : '?' }}</span>
                                    <span class="fw-semibold text-primary" style="font-size: 0.75rem;">{{ $percent }}%</span>
                                </div>

                                <!-- Progress bar completamento -->
                                <div class="progress mb-2" style="height: 4px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percent }}%;"></div>
                                </div>

                                <!-- 🌟 Indicatore dinamico del tempo rimanente -->
                                @if($currentFilter === 'watching' && $remainingEp > 0)
                                <div class="text-muted text-xs" style="font-size: 0.75rem;">
                                    ⏳ Rimangono: <strong>{{ $remHours > 0 ? $remHours.'h ' : '' }}{{ $remMins }}m</strong>
                                </div>
                                @elseif($currentFilter === 'completed')
                                <div class="text-success text-xs fw-semibold" style="font-size: 0.75rem;">
                                    🎉 Serie Completata!
                                </div>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-4">
                    <p class="text-muted">💨 Nessun anime trovato in questa categoria.</p>
                </div>
                @endforelse
                <div class="col-12 text-center py-4">
                    <p class="text-muted">💨 Nessun anime trovato in questa categoria.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>