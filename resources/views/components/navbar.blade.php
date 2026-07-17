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
                    <a class="nav-link {{ request()->is('dashboard*') ? 'active fw-bold text-primary' : '' }}" href="{{ route('dashboard') }}">La mia Lista</a>
                </li>

                <!-- Nome Utente (Disattivato come link, serve solo da indicatore) -->
                <li class="nav-item">
                    <span class="nav-link text-dark fw-semibold">Ciao, {{ auth()->user()->name }}</span>
                </li>

                <!-- Form di Logout (Obbligatorio POST in Laravel per sicurezza) -->
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm ms-lg-2">Esci</button>
                    </form>
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