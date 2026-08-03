@props(['platforms' => []])

@if(!empty($platforms))
<div class="streaming-providers my-3 p-3 bg-dark bg-opacity-75 rounded-3 border border-secondary border-opacity-50 shadow-sm">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="fw-bold fs-6 text-white">
            📺 Guarda Ora Su (Ufficiale)
        </span>
        <small class="text-muted fs-xs">Jikan / MAL</small>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @foreach($platforms as $platform)
        <a href="{{ $platform['url'] }}" target="_blank" rel="noopener noreferrer"
            class="badge {{ $platform['badge_color'] }} text-decoration-none p-2 d-flex align-items-center gap-1 shadow-sm-hover">
            <span>{{ $platform['icon'] }}</span>
            <span class="fw-semibold">{{ $platform['name'] }}</span>
            <span class="fs-xs opacity-75">↗</span>
        </a>
        @endforeach
    </div>
</div>
@else
<div class="my-2 p-2 bg-dark bg-opacity-25 rounded border border-secondary border-opacity-25 text-center">
    <small class="text-muted">🚫 Nessun link di streaming ufficiale trovato per questo titolo.</small>
</div>
@endif