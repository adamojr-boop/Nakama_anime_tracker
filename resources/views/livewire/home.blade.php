<x-app-layout>
    <x-slot:title>Nakama</x-slot:title>

    <div class="container py-5">
        <div class="row">
            <div class="col-12" data-aos="fade-up">
                <div class="card p-5 shadow-sm">
                    <h1 style="font-family: 'Cherry Bomb One', cursive;" class="text-primary">Benvenuto su Nakama!</h1>

                    <p class="lead">Inizia a connetterti con la tua cerchia e tieni traccia dei tuoi Eroi.</p>

                    @livewire('AnimeSearch')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>