<section class="mt-4">
    <h5 class="mb-3">Dove guardarlo</h5>

    @if(count($providers))
        <div class="d-flex flex-wrap gap-2">
            @foreach($providers as $provider)
                <a href="{{ $provider['url'] }}" target="_blank" rel="noopener noreferrer" class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 px-2 py-2 text-decoration-none">
                    @if($provider['logo_path'])
                        <img src="https://image.tmdb.org/t/p/w92{{ $provider['logo_path'] }}" alt="" width="24" height="24" class="rounded">
                    @endif
                    {{ $provider['name'] }}
                </a>
            @endforeach
        </div>
    @else
        <p class="text-muted small mb-0">Nessuna disponibilità streaming rilevata in Italia.</p>
    @endif

    @if($trailerUrl)
        <a href="{{ $trailerUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger btn-sm mt-3">
            Guarda il trailer ufficiale
        </a>
    @endif
</section>