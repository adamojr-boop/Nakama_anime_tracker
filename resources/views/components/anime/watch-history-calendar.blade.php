<div class="card bg-dark text-white border-secondary border-opacity-25 shadow-sm mb-4">
    <!-- Header / Barra Sempre Visibile -->
    <div class="card-header bg-dark bg-opacity-75 border-secondary border-opacity-25 p-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-white">📅 Diario di Visione</h5>
            <span class="badge bg-warning text-dark text-capitalize fw-bold">
                {{ $monthTitle }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Pulsante per Espandere / Comprimere -->
            <button class="btn btn-sm btn-outline-warning fw-bold d-flex align-items-center gap-1"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#watchCalendarCollapse"
                aria-expanded="false"
                aria-controls="watchCalendarCollapse">
                <span>Visualizza Calendario</span>
                <small class="ms-1">▼</small>
            </button>
        </div>
    </div>

    <!-- Contenuto Collapsabile (Chiuso di Default) -->
    <div class="collapse" id="watchCalendarCollapse" wire:ignore.self>
        <div class="card-body p-3">
            <!-- Controlli Mese (Precedente / Successivo) -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button wire:click="previousMonth" class="btn btn-outline-light btn-sm">
                    &laquo; Precedente
                </button>
                <span class="text-muted fs-xs text-uppercase fw-bold">Seleziona Mese</span>
                <button wire:click="nextMonth" class="btn btn-outline-light btn-sm">
                    Successivo &raquo;
                </button>
            </div>

            <!-- Griglia Giorni della Settimana -->
            <div class="row g-1 text-center fw-bold text-muted mb-2 text-uppercase fs-xs">
                <div class="col">Lun</div>
                <div class="col">Mar</div>
                <div class="col">Mer</div>
                <div class="col">Gio</div>
                <div class="col">Ven</div>
                <div class="col">Sab</div>
                <div class="col">Dom</div>
            </div>

            <!-- Griglia Calendario -->
            <div class="row g-2">
                @foreach(array_chunk($calendarGrid, 7) as $week)
                <div class="col-12">
                    <div class="row g-2">
                        @foreach($week as $dayData)
                        <div class="col" style="min-height: 85px;">
                            @if($dayData)
                            <div class="h-100 p-1 p-md-2 rounded border transition-all position-relative 
                                            {{ $dayData['is_today'] ? 'border-warning bg-warning bg-opacity-10' : 'border-secondary border-opacity-25 bg-dark bg-opacity-50' }}
                                            {{ !empty($dayData['logs']) ? 'cursor-pointer hover-border-warning' : '' }}"
                                @if(!empty($dayData['logs'])) wire:click="selectDay('{{ $dayData['date'] }}', {{ json_encode($dayData['logs']) }})" @endif>

                                <!-- Numero Giorno e Badge Progresso -->
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge {{ $dayData['is_today'] ? 'bg-warning text-dark' : 'bg-secondary bg-opacity-50 text-light' }} fs-xs">
                                        {{ $dayData['day'] }}
                                    </span>
                                    @if(count($dayData['logs']) > 0)
                                    <span class="badge bg-success fs-xs">+{{ count($dayData['logs']) }}</span>
                                    @endif
                                </div>

                                <!-- Miniature Anime Visti -->
                                <div class="d-flex flex-wrap gap-1 mt-1 overflow-hidden" style="max-height: 45px;">
                                    @foreach(array_slice($dayData['logs'], 0, 2) as $log)
                                    @if($log['image_url'])
                                    <img src="{{ $log['image_url'] }}"
                                        class="rounded object-fit-cover shadow-sm"
                                        style="width: 22px; height: 30px;"
                                        title="{{ $log['title'] }} (Ep. {{ $log['episode_number'] }})">
                                    @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-xs" style="width: 22px; height: 30px;">
                                        {{ $log['episode_number'] }}
                                    </div>
                                    @endif
                                    @endforeach
                                    @if(count($dayData['logs']) > 2)
                                    <div class="bg-dark rounded border border-secondary d-flex align-items-center justify-content-center text-light fs-xs" style="width: 22px; height: 30px;">
                                        +{{ count($dayData['logs']) - 2 }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @else
                            <!-- Cella Vuota -->
                            <div class="h-100 rounded border border-secondary border-opacity-10 bg-dark bg-opacity-10 opacity-25"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modale Dettaglio Giorno (Invariato) -->
    @if($selectedDayLogs)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.7);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border border-warning shadow">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">
                        📖 Visioni del {{ $selectedDateFormatted }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeDayDetails"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group list-group-flush">
                        @foreach($selectedDayLogs as $log)
                        <div class="list-group-item bg-transparent text-white border-secondary border-opacity-25 d-flex align-items-center gap-3 py-2">
                            @if($log['image_url'])
                            <img src="{{ $log['image_url'] }}" class="rounded object-fit-cover" style="width: 40px; height: 55px;" alt="{{ $log['title'] }}">
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $log['title'] }}</h6>
                                <small class="text-warning">Episodio {{ $log['episode_number'] }}</small>
                            </div>
                            <span class="badge bg-secondary opacity-75 fs-xs">
                                🕒 {{ $log['watched_time'] }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" wire:click="closeDayDetails">Chiudi</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>