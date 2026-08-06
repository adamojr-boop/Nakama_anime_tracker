<div class="position-relative" style="max-width: 300px;">
    <input
        type="text"
        wire:model.live.debounce.300ms="query"
        class="form-control bg-dark text-white border-secondary rounded-pill px-3"
        placeholder="🔍 Cerca utenti...">

    @if(strlen(trim($query)) >= 2)
    <div class="position-absolute start-0 end-0 mt-2 bg-dark border border-secondary rounded-3 shadow-lg z-3 overflow-hidden">
        @forelse($users as $searchUser)
        @php
        $avatar = $searchUser->profile && $searchUser->profile->avatar
        ? asset('storage/' . $searchUser->profile->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($searchUser->name) . '&background=0d6efd&color=fff';
        $username = $searchUser->profile->username ?? \Illuminate\Support\Str::slug($searchUser->name);
        @endphp

        <a href="{{ route('profile.show', $searchUser->id) }}" class="d-flex align-items-center gap-2 p-2 text-decoration-none text-white hover-bg-secondary border-bottom border-secondary border-opacity-25">
            <img src="{{ $avatar }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
            <div class="text-truncate">
                <div class="fw-bold fs-sm text-white">{{ $searchUser->name }}</div>
                <div class="text-muted fs-xs">@ {{ $username }}</div>
            </div>
        </a>
        @empty
        <div class="p-3 text-center text-muted fs-xs">
            Nessun utente trovato per "{{ $query }}"
        </div>
        @endforelse
    </div>
    @endif
</div>