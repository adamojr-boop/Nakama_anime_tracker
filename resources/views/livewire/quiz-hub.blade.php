<div class="mt-4">
    <h3 class="fw-bold mb-3">🧠 Quiz Hub</h3>

    @if($quizzes->isEmpty())
    <div class="card border-0 shadow-sm p-3">
        <p class="mb-0 text-muted">Nessun quiz disponibile al momento.</p>
    </div>
    @else
    <div class="row g-3">
        @foreach($quizzes as $quiz)
        <div class="col-md-6 col-xl-4" wire:key="quiz-{{ $quiz->id }}">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <span class="badge bg-secondary align-self-start mb-2">{{ ucfirst($quiz->difficulty) }}</span>
                    <h5 class="fw-bold mb-1">{{ $quiz->title }}</h5>
                    <p class="text-muted small mb-3">{{ $quiz->description ?: 'Metti alla prova la tua conoscenza anime.' }}</p>

                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="small text-muted">{{ $quiz->questions_count }} domande</span>
                        <a href="{{ route('quiz.play', $quiz->id) }}" class="btn btn-sm btn-primary">Gioca</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
