<div class="container py-4">
    <!-- HERO / BANNER COMMUNITY -->
    <div class="card bg-dark text-white border-0 shadow-lg rounded-4 overflow-hidden mb-4"
        style="background: linear-gradient(135deg, #1e1e2f 0%, #0d1117 100%);">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill mb-3">
                        🌐 Nakama Hub
                    </span>
                    <h1 class="fw-bold display-5 mb-2">Community & Chiacchiere</h1>
                    <p class="text-light-50 fs-5 mb-4">
                        Connettiti con altri appassionati, entra nel nostro server Discord e partecipa alle discussioni del Forum.
                    </p>

                    <!-- BARRA DI RICERCA MEMBRI -->
                    <div class="position-relative style-search" style="max-width: 500px;">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-lg bg-dark text-white border-secondary rounded-pill px-4 ps-5"
                            placeholder="🔍 Cerca membri per nome...">
                    </div>
                </div>

                <!-- WIDGET DISCORD -->
                <div class="col-md-5 mt-4 mt-md-0">
                    <div class="card bg-secondary bg-opacity-10 border-secondary rounded-4 p-4 text-center shadow-sm">
                        <div class="fs-1 mb-2">👾</div>
                        <h5 class="fw-bold text-white mb-1">Server Discord Ufficiale</h5>
                        <p class="text-muted small mb-3">Entra nel server per chattare in tempo reale con la community!</p>

                        <!-- WIDGET DISCORD EMBED / WIDGET BOT -->
                        <div class="d-grid gap-2">
                            <a href="https://discord.gg/YOUR_INVITE_LINK" target="_blank" class="btn btn-indigo btn-primary fw-bold rounded-pill shadow-sm">
                                🚀 Unisciti al Server
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEZIONE ESPLORA MEMBRI -->
    <div class="mb-5">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold text-white mb-0">👥 {{ strlen($search) >= 2 ? 'Risultati Ricerca' : 'Membri della Community' }}</h4>
            <span class="text-muted small">{{ $users->count() }} utenti mostrati</span>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3">
            @forelse($users as $communityUser)
            @php
            $avatar = $communityUser->profile && $communityUser->profile->avatar
            ? asset('storage/' . $communityUser->profile->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($communityUser->name) . '&background=0d6efd&color=fff';
            $isFollowing = auth()->check() && auth()->user()->isFollowing($communityUser);
            @endphp

            <div class="col">
                <div class="card bg-dark text-white border-secondary rounded-4 h-100 shadow-sm text-center p-3">
                    <a href="{{ route('profile.user', $communityUser->id) }}" class="text-decoration-none text-white">
                        <img src="{{ $avatar }}" class="rounded-circle mb-2" style="width: 70px; height: 70px; object-fit: cover;">
                        <h6 class="fw-bold mb-0 text-truncate">{{ $communityUser->name }}</h6>
                    </a>

                    @if(auth()->check() && auth()->id() !== $communityUser->id)
                    <div class="mt-auto">
                        <a href="{{ route('profile.user', $communityUser->id) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100 fw-bold">
                            Vedi Profilo
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="p-4 text-center border border-secondary border-dashed rounded-4 text-muted">
                    Nessun membro trovato con le credenziali cercate.
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- SEZIONE DISCUSSIONI / FORUM -->
    <div class="card bg-dark text-white border-secondary shadow-sm rounded-4">
        <div class="card-header border-secondary bg-transparent p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <h4 class="fw-bold mb-0">💬 Bacheca Discussioni</h4>

            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-primary rounded-pill btn-sm px-3 fw-bold">
                    ➕ Nuova Discussione
                </button>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- PLACEHOLDER DISCUSSIONI -->
            <div class="text-center py-5 text-muted">
                <div class="fs-1 mb-2">🗣️</div>
                <h5>La bacheca è pronta per accogliere i primi post!</h5>
                <p class="small">Inizia una nuova conversazione su Anime, Manga o argomenti Off-Topic.</p>
            </div>
        </div>
    </div>
</div>