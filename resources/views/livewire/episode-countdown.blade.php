<div class="card bg-dark text-white border-secondary p-3 shadow-sm mb-4">
    @if ($episode)
    {{-- STATO 1: Episodio Rilasciato (Badge Disponibile) --}}
    @if ($episode->status === 'released')
    <div class="text-center py-2">
        <span class="badge bg-success text-white uppercase px-3 py-2 fs-6 mb-2">
            ✓ EPISODIO DISPONIBILE
        </span>
        <h5 class="fw-bold text-white mb-1">{{ $episode->title }}</h5>
        <p class="text-muted small mb-0">Episodio {{ $episode->episode_number }} rilasciato ufficialmente.</p>
    </div>

    {{-- STATO 2: Countdown in Corso --}}
    @else
    <div
        x-data="countdownTimer('{{ $formattedAirDate }}')"
        x-init="initTimer()">
        <template x-if="!isExpired">
            <div>
                <div class="text-center mb-3">
                    <span class="badge bg-danger text-white uppercase mb-1">
                        In Arrivo • Ep. {{ $episode->episode_number }}
                    </span>
                    <h5 class="fw-bold text-white mb-0 mt-1">
                        {{ $episode->title }}
                    </h5>
                </div>

                <div class="row g-2 text-center">
                    <div class="col-3">
                        <div class="bg-secondary bg-opacity-25 p-2 rounded">
                            <div class="fs-4 fw-bold text-white" x-text="days">00</div>
                            <div class="text-uppercase text-muted" style="font-size: 10px;">Giorni</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-secondary bg-opacity-25 p-2 rounded">
                            <div class="fs-4 fw-bold text-white" x-text="hours">00</div>
                            <div class="text-uppercase text-muted" style="font-size: 10px;">Ore</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-secondary bg-opacity-25 p-2 rounded">
                            <div class="fs-4 fw-bold text-white" x-text="minutes">00</div>
                            <div class="text-uppercase text-muted" style="font-size: 10px;">Minuti</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="bg-secondary bg-opacity-25 p-2 rounded">
                            <div class="fs-4 fw-bold text-danger" x-text="seconds">00</div>
                            <div class="text-uppercase text-muted" style="font-size: 10px;">Sec</div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="isExpired">
            <div class="text-center py-2">
                <span class="badge bg-success text-white uppercase px-3 py-2 fs-6 mb-1">
                    ✓ EPISODIO DISPONIBILE
                </span>
                <p class="text-muted small mb-0">Episodio {{ $episode->episode_number }} disponibile ora!</p>
            </div>
        </template>
    </div>
    @endif
    @else
    <div class="text-center text-muted">
        📺 Nessun episodio imminente in palinsesto
    </div>
    @endif
</div>

{{-- Best Practice Livewire 3: Lo script viene spostato automaticamente nel footer --}}
@script
<script>
    Alpine.data('countdownTimer', (targetUtcDate) => ({
        targetTime: new Date(targetUtcDate).getTime(),
        days: '00',
        hours: '00',
        minutes: '00',
        seconds: '00',
        isExpired: false,
        timer: null,

        initTimer() {
            this.calculate();
            this.timer = setInterval(() => this.calculate(), 1000);
        },

        calculate() {
            const now = Date.now();
            const diff = this.targetTime - now;

            if (diff <= 0) {
                this.isExpired = true;
                clearInterval(this.timer);
                $wire.call('markAsReleased');
                return;
            }

            const pad = (n) => String(n).padStart(2, '0');

            this.days = pad(Math.floor(diff / (1000 * 60 * 60 * 24)));
            this.hours = pad(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)));
            this.minutes = pad(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60)));
            this.seconds = pad(Math.floor((diff % (1000 * 60)) / 1000));
        }
    }));
</script>
@endscript