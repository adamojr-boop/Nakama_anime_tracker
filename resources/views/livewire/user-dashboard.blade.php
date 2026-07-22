<div class="container mt-4">
    <!-- Header di benvenuto -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">こんにちは, {{ auth()->user()->name }}! </h1>
            <p class="text-muted mb-0">Bentornato nella tua area Nakama. Ecco la tua lista anime.</p>
        </div>
    </div>

    <!-- Widgets Livewire -->
    <div class="mb-4">
        <livewire:user-stats-widget />

        <div class="card p-3 shadow-sm border-0 bg-dark text-white mb-3">
            <div class="d-flex align-items-center">
                <div class="fs-1 me-3">🍿</div>
                <div>
                    <h6 class="mb-0 text-muted">Record Maratona</h6>
                    <h4 class="fw-bold mb-0">{{ $maxBinge }} Episodi Consecutivi</h4>
                </div>
            </div>
        </div>
    </div>

    <livewire:user-badges-widget />
    <livewire:quiz-hub />

    <!-- Classifica Community -->
    <div class="row mt-4">
        <div class="col-lg-4">
            <h3 class="fw-bold mb-3">🏆 Top Giocatori</h3>
            <div class="card bg-dark text-white border-secondary p-3 shadow-sm">
                <ul class="list-group list-group-flush bg-transparent">
                    @forelse($leaderboard as $entry)
                    <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center border-secondary px-0">
                        <div>
                            <span class="fw-bold me-2">#{{ $loop->iteration }}</span>
                            {{ $entry['name'] ?? 'Utente' }}
                        </div>
                        <span class="badge bg-warning text-dark fs-6">{{ number_format((int) ($entry['points'] ?? $entry['total_score'] ?? 0)) }} PTS</span>
                    </li>
                    @empty
                    <li class="list-group-item bg-transparent text-white border-secondary px-0">Nessun utente in classifica.</li>
                    @endforelse
                </ul>
            </div>
            <div class="my-4">
                <div class="card border-0 shadow-sm p-3 d-flex flex-md-row align-items-md-center justify-content-between gap-3">
                    <a href="{{ route('user.lists') }}" class="btn btn-primary fw-semibold px-4">Vai alle mie liste</a>
                </div>
            </div>
        </div>
    </div>
</div>