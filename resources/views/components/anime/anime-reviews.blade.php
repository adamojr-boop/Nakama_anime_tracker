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

        <div class="mb-3">
            <label class="form-label fw-semibold">Voto (da 1 a 10)</label>
            <select wire:model="rating" class="form-select" style="max-width: 120px;">
                @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">{{ $i }} ★</option>
                    @endfor
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Il tuo commento</label>
            <textarea wire:model="comment" class="form-control" rows="3" placeholder="Cosa ne pensi di questo anime? Evita gli spoiler..."></textarea>
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
    <!-- LISTA DEI COMMENTI REALI DA MYSQL -->
    <div class="reviews-list">
        @forelse($reviews as $review)
        <div class="card mb-3 shadow-sm border-0 bg-white">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong class="text-primary">👤 {{ $review->user->name }}</strong>
                        <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                    <span class="badge bg-warning text-dark">★ {{ $review->rating }}/10</span>
                </div>
                <p class="card-text mb-0 text-secondary" style="white-space: pre-line;">{{ $review->comment }}</p>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-3">
            <p class="mb-0">Nessuno ha ancora recensito questo anime. Fai il primo passo!</p>
        </div>
        @endforelse
    </div>
</div>