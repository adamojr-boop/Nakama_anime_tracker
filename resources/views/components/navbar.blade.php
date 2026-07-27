<nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <img src="{{ asset('media/logo2.png') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
            <span style="font-family: 'Agbalumo', system-ui; font-size: 1.4rem;" class="text-primary">Nakama</span>
        </a>

        <!-- Bottone Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu di Navigazione -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto gap-2 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('/') ? 'active fw-bold text-primary' : '' }}" href="/">Home</a>
                </li>

                @auth
                <!-- SE L'UTENTE È LOGGATO -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard*') ? 'active fw-bold text-primary' : '' }}" href="/dashboard">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.lists') ? 'active fw-bold text-primary' : '' }}" href="{{ route('user.lists') }}">Le mie Liste</a>
                </li>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tema Piattaforma</label>
                    <select wire:model="theme" class="form-select">
                        <option value="dark">🌙 Scuro (Dark)</option>
                        <option value="light">☀️ Chiaro (Light)</option>
                    </select>
                </div>

                <!-- DROPDOWN PROFILO UTENTE -->
                @php
                $authUser = auth()->user();
                $userProfile = $authUser->profile;
                $navAvatar = $userProfile && $userProfile->avatar
                ? asset('storage/' . $userProfile->avatar)
                : 'https://ui-avatars.com/api/?name=' . urlencode($authUser->name) . '&background=0d6efd&color=fff';
                @endphp

                <li class="nav-item dropdown ms-lg-2">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 {{ request()->routeIs('profile.*') ? 'active fw-bold text-primary' : '' }}"
                        href="#"
                        id="navbarProfileDropdown"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="{{ $navAvatar }}" alt="{{ $authUser->name }}" width="30" height="30" class="rounded-circle border border-primary" style="object-fit: cover;">
                        <span class="fw-semibold">{{ $authUser->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="navbarProfileDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                                👤 Il Mio Profilo
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                ⚙️ Impostazioni Profilo
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                    🚪 Esci
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <!-- SE L'UTENTE È UN OSPITE -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('login') ? 'active fw-bold text-primary' : '' }}" href="{{ route('login') }}">Accedi</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-primary btn-sm text-white ms-lg-2 {{ request()->is('register') ? 'disabled' : '' }}" href="{{ route('register') }}">Registrati</a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>