<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="OneCircle, il social dove ogni connessione conta.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Nakama' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('media/logo2.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('media/logo2.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Agbalumo&family=Cherry+Bomb+One&display=swap" rel="stylesheet">

    <script>
        // Applied before first paint to avoid a light/dark flash on load.
        (function () {
            var stored = localStorage.getItem('nakama-theme');
            var theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="d-flex flex-column min-vh-100">

    <x-navbar />
    <x-sidebar />

    <main class="flex-grow-1">
        {{ $slot ?? '' }}
    </main>

    <x-footer />

    @livewireScripts
</body>

</html>