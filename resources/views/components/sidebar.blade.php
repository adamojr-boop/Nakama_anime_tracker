<div class="offcanvas offcanvas-start" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel" style="--bs-offcanvas-width: 270px;">
    <div class="offcanvas-header border-bottom">
        <a href="/" class="d-flex align-items-center gap-2 text-decoration-none" id="appSidebarLabel">
            <img src="{{ asset('media/logo2.png') }}" alt="Logo" width="28" height="28">
            <span class="brand-font text-primary">Nakama</span>
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Chiudi"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-2">
        <ul class="nav nav-pills flex-column gap-1 mb-auto">
            <li class="nav-item">
                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> Home
                </a>
            </li>

            @auth
            <li class="nav-item">
                <a href="/dashboard" class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('community.index') }}" class="nav-link {{ request()->routeIs('community.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> Community
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('user.lists') }}" class="nav-link {{ request()->routeIs('user.lists') ? 'active' : '' }}">
                    <i class="bi bi-bookmark-star-fill"></i> Le mie Liste
                </a>
            </li>

            <li><hr class="my-2 text-body-secondary"></li>

            <li class="nav-item">
                <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i> Il Mio Profilo
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i> Impostazioni
                </a>
            </li>
            @else
            <li><hr class="my-2 text-body-secondary"></li>
            <li class="nav-item">
                <a href="{{ route('login') }}" class="nav-link {{ request()->is('login') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-right"></i> Accedi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('register') }}" class="nav-link {{ request()->is('register') ? 'active' : '' }}">
                    <i class="bi bi-person-plus-fill"></i> Registrati
                </a>
            </li>
            @endauth
        </ul>

        @auth
        <form action="{{ route('logout') }}" method="POST" class="mt-2 pt-2 border-top">
            @csrf
            <button type="submit" class="nav-link text-danger w-100 text-start border-0 bg-transparent">
                <i class="bi bi-box-arrow-right"></i> Esci
            </button>
        </form>
        @endauth
    </div>
</div>
