<div class="card card-body shadow-sm border-0 rounded-3 mb-4">
    <!-- Header con Titolo e Counter Totale -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-secondary d-flex align-items-center gap-2">
            🏆 Bacheca Trofei & Badge
        </h5>
        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill shadow-sm fw-bold">
            {{ $unlockedCount }} / {{ $totalBadges }} Sbloccati
        </span>
    </div>
    <!-- Griglia Trofei -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        @foreach($allBadges as $badge)
        @php
        $isUnlocked = array_key_exists($badge->id, $unlockedBadges);
        $unlockedAt = $isUnlocked ? \Carbon\Carbon::parse($unlockedBadges[$badge->id]) : null;
        @endphp

        <div class="col">
            <div class="card h-100 border-0 p-3 shadow-sm rounded-3 text-center position-relative transition-all {{ $isUnlocked ? 'bg-light border-start border-warning border-4' : 'bg-white opacity-50 grayscale' }}"
                style="transition: transform 0.2s;"
                onmouseover="this.style.transform='scale(1.02)'"
                onmouseout="this.style.transform='scale(1)'">
                <!-- Icona Trofeo -->
                <div class="display-4 mb-2">
                    {{ $badge->icon }}
                </div>
                <!-- Nome Trofeo -->
                <h6 class="fw-bold mb-1 {{ $isUnlocked ? 'text-dark' : 'text-muted' }}">
                    {{ $badge->name }}
                </h6>
                <!-- Descrizione -->
                <p class="small text-muted mb-2" style="font-size: 0.8rem;">
                    {{ $badge->description }}
                </p>
                <!-- Stato Sblocco / Data -->
                <div class="mt-auto">
                    @if($isUnlocked)
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                        Sbloccato il {{ $unlockedAt->format('d/m/Y') }}
                    </span>
                    @else
                    <span class="badge bg-secondary-subtle text-muted rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                        🔒 Bloccato
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>