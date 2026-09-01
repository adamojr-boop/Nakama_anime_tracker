<div class="mt-5 border-top pt-4">
    <h3 class="mb-4">💬 Recensioni della Community</h3>

    <!-- Messaggi di successo o errore -->
    @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- FORM DI INSERIMENTO (Solo per utenti registrati) -->
    @auth
    <form wire:submit.prevent="saveReview" class="card card-body shadow-sm mb-4">
        <h5 class="card-title">Lascia la tua recensione</h5>

        <div class="mb-3 d-flex align-items-center gap-4">
            <div>
                <label class="form-label fw-semibold mb-1">Voto (da 1 a 10)</label>
                <select wire:model="rating" class="form-select" style="max-width: 120px;">
                    @for ($i = 1; $i <= 10; $i++)
                        <option value="{{ $i }}">{{ $i }} ★</option>
                        @endfor
                </select>
            </div>

            <!-- Toggle Spoiler -->
            <div class="form-check form-switch m-0 pt-4">
                <input class="form-check-input" type="checkbox" id="spoilerCheck" wire:model="isSpoiler">
                <label class="form-check-label small text-warning fw-bold ms-1" for="spoilerCheck">
                    ⚠️ Contiene Spoiler
                </label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Il tuo commento</label>
            <textarea wire:model="comment" class="form-control" rows="3" placeholder="Cosa ne pensi di questo anime?"></textarea>
            @error('comment') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-sm align-self-start">Pubblica Recensione</button>
    </form>
    @else
    <div class="alert alert-light border text-center mb-4 p-3">
        <p class="mb-2 text-muted">Vuoi dire la tua su questo anime?</p>
        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Accedi per commentare</a>
    </div>
    @endauth

    <!-- LISTA DEI COMMENTI -->
    <div class="reviews-list">
        @forelse($reviews as $review)
        @php($isRevealed = in_array($review->id, $revealedReviewIds, true))
        <div class="card mb-3 shadow-sm border-0 bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong class="text-primary">👤 {{ $review->user->name }}</strong>
                        <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                        @if($review->is_spoiler)
                        <span class="badge bg-warning text-dark ms-2">⚠️ Spoiler</span>
                        @endif
                    </div>
                    <span class="badge bg-warning text-dark">★ {{ $review->rating }}/10</span>
                </div>

                <!-- Wrapper con sfocatura condizionale -->
                <div class="position-relative overflow-hidden rounded">
                    <p @class([
                        'card-text mb-0 text-secondary',
                        'filter-blur' => $review->is_spoiler && ! $isRevealed,
                    ])
                        style="white-space: pre-line; transition: filter 0.3s ease;">
                        {{ $review->comment }}
                    </p>

                    <!-- Overlay Spoiler da cliccare -->
                    @if($review->is_spoiler && ! $isRevealed)
                    <button type="button"
                        wire:click="revealReview({{ $review->id }})"
                        class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center border-0 bg-dark bg-opacity-50 text-white rounded"
                        style="cursor: pointer; z-index: 2;">
                        <span class="badge bg-danger fs-6 px-3 py-2 shadow">
                            ⚠️ Clicca per mostrare lo spoiler
                        </span>
                    </button>
                    @endif
                </div>

            </div>
        </div>
        @empty
        <div class="text-center text-muted py-3">
            <p class="mb-0">Nessuno ha ancora recensito questo anime. Fai il primo passo!</p>
        </div>
        @endforelse
    </div>
</div>