<div class="card border-0 shadow-sm mb-4 bg-white rounded-3 overflow-hidden">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3 text-secondary d-flex align-items-center gap-2">
            📊 Statistiche di Visione & Livelli
        </h5>

        <div class="row g-3 text-center">
            <!-- Tempo Totale -->
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 d-flex flex-column justify-content-center">
                    <span class="text-muted small text-uppercase fw-bold">Tempo Totale Speso</span>
                    <div class="fs-4 fw-bold text-primary mt-1">
                        @if($stats['days'] > 0) {{ $stats['days'] }}d @endif
                        {{ $stats['hours'] }}h {{ $stats['minutes'] }}m
                    </div>
                    <span class="small text-muted mt-1">
                        {{ number_format($stats['total_episodes']) }} episodi visti in totale
                    </span>
                </div>
            </div>
            <!-- Contatore Rewatch -->
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 d-flex flex-column justify-content-center">
                    <span class="text-muted small text-uppercase fw-bold">Serie Riavviate (Rewatch)</span>
                    <div class="fs-4 fw-bold text-warning mt-1">
                        🔄 {{ $stats['total_rewatches'] }}
                    </div>
                    <span class="small text-muted mt-1">
                        Volte in cui hai completato di nuovo un anime
                    </span>
                </div>
            </div>
            <!-- Livello & Badge Gamificato -->
            <div class="col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 d-flex flex-column justify-content-center position-relative">
                    <span class="text-muted small text-uppercase fw-bold">Livello Fedeltà</span>
                    <div class="fs-5 fw-bold text-dark mt-1">
                        {{ $stats['badge'] }} {{ $stats['title'] }} <span class="badge bg-dark ms-1">Lvl {{ $stats['level'] }}</span>
                    </div>
                    <!-- Progress Bar per il prossimo livello -->
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $stats['progress_percent'] }}%;"
                            aria-valuenow="{{ $stats['progress_percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="small text-muted mt-1" style="font-size: 0.75rem;">
                        @if($stats['next_target'] === 'Max Level!')
                        🏆 Livello Massimo Raggiunto!
                        @else
                        Prossimo livello a {{ $stats['next_target'] }} rewatch
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>