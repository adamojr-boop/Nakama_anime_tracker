<div class="card bg-dark text-white p-4 shadow-sm border-secondary"
    x-data="{ openRating: true, openDiscussion: true, composerMenuOpen: false, activePicker: null }">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0 fw-bold">⭐ Valutazione Episodio {{ $episodeNumber }}</h5>
        <button type="button"
            class="btn btn-sm btn-outline-light"
            @click="openRating = !openRating">
            <span x-text="openRating ? 'Chiudi' : 'Apri'"></span>
        </button>
    </div>

    <div x-show="openRating" x-transition>
        @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form wire:submit.prevent="save">
            <div class="mb-3">
                <label class="form-label fw-bold">Voto (1 - 10)</label>
                <div class="d-flex flex-wrap gap-1">
                    @for ($i = 1; $i <= 10; $i++)
                    <button type="button"
                        wire:click="$set('rating', {{ $i }})"
                        class="btn btn-sm {{ $rating == $i ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ $i }}
                    </button>
                    @endfor
                </div>
                @error('rating') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Emozione Prevalente</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($emotions as $key => $emo)
                    <button type="button"
                        wire:click="$set('emotion', '{{ $key }}')"
                        class="btn btn-sm {{ $emotion == $key ? 'btn-warning text-dark fw-bold' : 'btn-outline-light' }}">
                        {{ $emo['emoji'] }} {{ $emo['label'] }}
                    </button>
                    @endforeach
                </div>
                @error('emotion') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label for="favorite_character" class="form-label fw-bold">Personaggio Preferito</label>
                <input type="text"
                    id="favorite_character"
                    wire:model="favorite_character"
                    class="form-control bg-dark text-white border-secondary"
                    placeholder="Es. Eren Yeager, Gojo Satoru...">
                @error('favorite_character') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold">
                Salva Valutazione
            </button>
        </form>
    </div>

    <hr class="border-secondary my-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0 fw-bold">💬 Discussione Episodio {{ $episodeNumber }}</h5>
        <button type="button"
            class="btn btn-sm btn-outline-light"
            @click="openDiscussion = !openDiscussion">
            <span x-text="openDiscussion ? 'Chiudi' : 'Apri'"></span>
        </button>
    </div>

    <div x-show="openDiscussion" x-transition>
        @if (session()->has('comment_success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
            {{ session('comment_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form wire:submit.prevent="postComment" class="mb-4">
            @if($parentId)
            <div class="badge bg-info text-white mb-2 d-inline-flex align-items-center gap-2">
                <span>Stai rispondendo a un commento</span>
                <button type="button" wire:click="cancelReply" class="btn-close btn-close-white" style="font-size: 0.65rem;"></button>
            </div>
            @endif

            <div class="mb-2 position-relative">
                <textarea wire:model.live="body"
                    rows="3"
                    class="form-control bg-dark text-white border-secondary shadow-none"
                    placeholder="Scrivi un commento... Usa @ per taggare"></textarea>
                @error('body') <span class="text-danger small">{{ $message }}</span> @enderror

                @if (!empty($this->mentionSuggestions))
                <div class="position-absolute start-0 end-0 mt-1 p-2 rounded border border-secondary bg-black z-3">
                    <div class="small text-white mb-1">Tag suggeriti</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($this->mentionSuggestions as $suggestion)
                        <button type="button"
                            wire:click="insertMention({{ $suggestion['id'] }})"
                            class="btn btn-sm btn-outline-info">
                            @{{ str_replace(' ', '_', $suggestion['name']) }}
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="d-flex justify-content-between align-items-center border-top border-secondary pt-2 position-relative">
                <div class="d-flex gap-2 align-items-center">
                    <button type="button"
                        class="btn btn-outline-secondary btn-sm rounded-circle fw-bold"
                        style="width: 36px; height: 36px;"
                        @click="composerMenuOpen = !composerMenuOpen">
                        +
                    </button>

                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        Pubblica
                    </button>
                </div>

                <div x-show="composerMenuOpen"
                    x-transition
                    @click.outside="composerMenuOpen = false"
                    class="position-absolute bottom-100 start-0 mb-2 p-2 bg-black rounded border border-secondary shadow-lg z-3">
                    <div class="d-flex flex-column gap-2" style="min-width: 180px;">
                        <button type="button"
                            class="btn btn-sm btn-outline-light text-start"
                            @click="$refs.commentImageInput.click(); composerMenuOpen = false; activePicker = null;">
                            📷 Immagine
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-outline-light text-start"
                            @click="activePicker = (activePicker === 'gif' ? null : 'gif'); composerMenuOpen = false;">
                            GIF Link
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-outline-light text-start"
                            @click="activePicker = (activePicker === 'timestamp' ? null : 'timestamp'); composerMenuOpen = false;">
                            ⏱ Timestamp
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-outline-light text-start"
                            @click="$wire.body = ($wire.body ? $wire.body + ' @' : '@'); composerMenuOpen = false; activePicker = null;">
                            👤 Tagga Utente
                        </button>
                    </div>
                </div>
            </div>

            <input type="file" x-ref="commentImageInput" wire:model="image" class="d-none" accept="image/*">

            <div class="mt-3" x-show="activePicker === 'gif'" x-transition>
                <label class="form-label small text-muted">URL GIF</label>
                <input type="url"
                    wire:model="gifUrl"
                    class="form-control form-control-sm bg-dark text-white border-secondary"
                    placeholder="https://media.giphy.com/...">
                @error('gifUrl') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="mt-3" x-show="activePicker === 'timestamp'" x-transition>
                <label class="form-label small text-white">Inserisci timestamp (mm:ss o hh:mm:ss)</label>
                <div class="d-flex gap-2">
                    <input type="text"
                        wire:model="timestampInput"
                        class="form-control form-control-sm bg-dark text-white border-secondary"
                        placeholder="es. 12:34">
                    <button type="button" wire:click="addTimestamp" class="btn btn-sm btn-outline-warning">Aggiungi</button>
                </div>
                @error('timestampInput') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex gap-3 mt-3">
                @if ($image)
                <div class="position-relative">
                    <span class="small text-white d-block mb-1">Immagine allegata:</span>
                    <img src="{{ $image->temporaryUrl() }}" class="rounded img-thumbnail bg-dark border-secondary" style="max-height: 80px;">
                    <button type="button" wire:click="$set('image', null)" class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1 rounded-circle" style="transform: translate(30%, -30%);">&times;</button>
                </div>
                @endif

                @if ($gifUrl)
                <div class="position-relative">
                    <span class="small text-white d-block mb-1">GIF collegata:</span>
                    <img src="{{ $gifUrl }}" class="rounded img-thumbnail bg-dark border-secondary" style="max-height: 80px;">
                    <button type="button" wire:click="$set('gifUrl', null)" class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1 rounded-circle" style="transform: translate(30%, -30%);">&times;</button>
                </div>
                @endif
            </div>
        </form>

        <div class="d-flex flex-column gap-3">
            @forelse ($comments as $comment)
            <div class="p-3 bg-secondary bg-opacity-10 rounded border border-secondary">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <strong class="text-info">{{ $comment->user->name }}</strong>
                        <span class="text-white small">• {{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    @if ($comment->user_id === auth()->id())
                    <button wire:click="deleteComment({{ $comment->id }})" class="btn btn-sm btn-outline-danger py-0 px-2">Elimina</button>
                    @endif
                </div>

                <p class="mb-2">{!! $this->formatBody($comment->body) !!}</p>

                @foreach ($comment->attachments as $attachment)
                <div class="my-2">
                    <img src="{{ $attachment->type === 'image' ? asset('storage/' . $attachment->file_path) : $attachment->file_path }}"
                        class="rounded img-fluid border border-secondary"
                        style="max-height: 250px;">
                </div>
                @endforeach

                <div class="mt-2">
                    <button wire:click="setReplyTo({{ $comment->id }})" class="btn btn-sm btn-link text-decoration-none p-0 text-white">Rispondi</button>
                </div>

                @if ($comment->replies->count() > 0)
                <div class="ms-4 mt-3 pt-2 border-start border-secondary ps-3">
                    @foreach ($comment->replies as $reply)
                    <div class="mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <strong class="text-info small">{{ $reply->user->name }}</strong>
                            <span class="text-white extra-small">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mb-1 small">{!! $this->formatBody($reply->body) !!}</p>

                        @foreach ($reply->attachments as $att)
                        <img src="{{ $att->type === 'image' ? asset('storage/' . $att->file_path) : $att->file_path }}"
                            class="rounded img-fluid border border-secondary my-1"
                            style="max-height: 180px;">
                        @endforeach
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <p class="text-white text-center my-3">Nessun commento presente. Sii il primo a commentare questo episodio!</p>
            @endforelse
        </div>
    </div>
</div>
