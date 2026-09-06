<nav class="navbar navbar-expand sticky-top border-bottom bg-body">
    <div class="container-fluid px-3 gap-2">
        <button type="button" class="btn-icon" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Apri il menu">
            <i class="bi bi-list fs-4"></i>
        </button>

        <a class="navbar-brand d-flex align-items-center gap-2 me-auto" href="/">
            <img src="{{ asset('media/logo2.png') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
            <span class="brand-font text-primary d-none d-sm-inline">Nakama</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            @auth
            <div class="d-none d-lg-block">
                @livewire('user-search')
            </div>
            <livewire:anime-recommendation-modal />
            @endauth

            <button type="button" id="themeToggle" class="btn-icon" aria-label="Cambia tema chiaro/scuro">
                <i class="bi bi-moon-stars-fill" data-theme-icon></i>
            </button>

            @auth
            @php
            $userProfile = auth()->user()->profile;
            $navAvatar = $userProfile && $userProfile->avatar
            ? asset('storage/' . $userProfile->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=6c5ce7&color=fff';
            @endphp
            <a href="{{ route('profile.show') }}" class="d-flex align-items-center" aria-label="Il mio profilo">
                <img src="{{ $navAvatar }}" alt="{{ auth()->user()->name }}" width="34" height="34" class="rounded-circle border" style="object-fit: cover;">
            </a>
            @else
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Accedi</a>
            <a href="{{ route('register') }}" class="btn btn-sm btn-primary d-none d-sm-inline-flex">Registrati</a>
            @endauth
        </div>
    </div>
</nav>