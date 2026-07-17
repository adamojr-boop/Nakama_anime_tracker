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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    @livewireStyles
</head>

<body>

    <x-navbar />

    {{ $slot ?? '' }}

    <x-footer />

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

    @livewireScripts
</body>

</html>