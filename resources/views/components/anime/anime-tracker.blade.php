<div class="card card-body shadow-sm mb-4 bg-light border-0">
    @auth
    <h5 class="fw-bold mb-3 text-secondary">🎬 Il tuo progresso</h5>

    <div class="d-flex align-items-center gap-3">
        <!-- Pulsante Meno -->
        <button wire:click="decrement" class="btn btn-outline-danger btn-lg rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;" @if($watchedEpisodes==0) disabled @endif>
            -
        </button>
        <!-- Contatore centrale -->
        <div class="text-center bg-white border rounded px-4 py-2 shadow-sm">
            <span class="fs-3 fw-bold text-dark">{{ $watchedEpisodes }}</span>
            <span class="text-muted fs-5"> / {{ $totalEpisodes == 9999 ? '?' : $totalEpisodes }}</span>
            <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.75rem;">Episodi Visti</div>
        </div>
        <!-- Pulsante Più -->
        <button wire:click="increment" class="btn btn-primary btn-lg rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;" @if($watchedEpisodes>= $totalEpisodes) disabled @endif>
            +
        </button>
    </div>
    <!-- Badge di completamento -->
    @if($watchedEpisodes == $totalEpisodes && $totalEpisodes != 9999)
    <div class="mt-3 alert alert-success py-2 text-center mb-0 border-0 fw-semibold text-success small">
        Hai completato questo anime!
    </div>
    @endif

    @else
    <div class="text-center py-2">
        <p class="text-muted mb-2 small">Vuoi tenere traccia degli episodi visti?</p>
        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Accedi per tracciare</a>
    </div>
    @endauth
</div>