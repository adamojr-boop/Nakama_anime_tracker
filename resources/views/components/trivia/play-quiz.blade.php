<div class="container py-4">
    @if(!$isFinished && isset($questions[$currentIndex]))
    @php $currentQuestion = $questions[$currentIndex]; @endphp

    <div class="card shadow border-0 bg-dark text-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge bg-primary fs-6">Domanda {{ $currentIndex + 1 }} di {{ $questions->count() }}</span>
            <span class="badge bg-warning text-dark fs-6">Punti: {{ $score }}</span>
        </div>

        <h4 class="mb-4">{{ $currentQuestion->question_text }}</h4>

        <div class="row g-3">
            @foreach($currentQuestion->options as $index => $option)
            <div class="col-md-6">
                <button
                    wire:click="selectOption({{ $index }})"
                    class="btn w-100 p-3 text-start border-secondary text-white 
                            {{ $selectedOption === $index ? 'btn-primary active' : 'btn-outline-light' }}">
                    <strong>{{ chr(65 + $index) }}.</strong> {{ $option }}
                </button>
            </div>
            @endforeach
        </div>

        <div class="mt-4 text-end">
            <button
                wire:click="nextQuestion"
                class="btn btn-success btn-lg px-4"
                {{ $selectedOption === null ? 'disabled' : '' }}>
                {{ $currentIndex + 1 == $questions->count() ? 'Concludi Quiz' : 'Prossima Domanda ➔' }}
            </button>
        </div>
    </div>
    @else
    <!-- Schermata Risultati e Sblocco Trofei -->
    <div class="card shadow border-0 bg-dark text-white text-center p-5">
        <div class="fs-1 mb-2">🎉</div>
        <h2 class="fw-bold">Quiz Completato!</h2>
        <p class="text-muted">Ecco i dettagli del tuo tentativo:</p>

        @if(!empty($unlockedBadges))
        <div class="alert alert-warning d-inline-block my-3 px-4 py-2">
            🏆 <strong>Nuovo Trofeo Sbloccato:</strong> {{ implode(', ', $unlockedBadges) }}
        </div>
        @endif

        <div class="row my-4 justify-content-center">
            <div class="col-md-4">
                <div class="p-3 bg-secondary rounded">
                    <h5>Risposte Corrette</h5>
                    <h3 class="fw-bold text-success">{{ $correctAnswersCount }} / {{ $questions->count() }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-secondary rounded">
                    <h5>Punteggio Totale</h5>
                    <h3 class="fw-bold text-warning">{{ $score }} PTS</h3>
                </div>
            </div>
        </div>

        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light me-2">Torna alla Dashboard</a>
            <button onclick="location.reload()" class="btn btn-primary">Riprova Quiz</button>
        </div>
    </div>
    @endif
</div>