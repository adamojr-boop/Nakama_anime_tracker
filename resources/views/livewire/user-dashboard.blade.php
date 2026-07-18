<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">こんにちは, {{ Auth::user()->name }}! </h1>
            <p class="text-muted">Bentornato nella tua area Nakama. Ecco la tua lista anime.</p>
        </div>
    </div>
    <!-- Menu Filtri (Aggiornato con la nuova Tab delle liste) -->
    <ul class="nav nav-pills mb-4 bg-light p-2 rounded-3 shadow-sm" style="max-width: max-content;">
        <li class="nav-item">
            <button wire:click="setFilter('watching')" class="nav-item nav-link fw-semibold px-4 py-2 border-0 rounded-3 {{ $currentFilter === 'watching' ? 'active btn-primary' : 'text-secondary' }}">
                📺 In Corso
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="setFilter('completed')" class="nav-item nav-link fw-semibold px-4 py-2 border-0 rounded-3 {{ $currentFilter === 'completed' ? 'active btn-primary' : 'text-secondary' }}">
                🎉 Completati
            </button>
        </li>
        <li class="nav-item">
            <button wire:click="setFilter('my-lists')" class="nav-item nav-link fw-semibold px-4 py-2 border-0 rounded-3 {{ $currentFilter === 'my-lists' ? 'active btn-primary' : 'text-secondary' }}">
                📂 Le mie Liste / Wishlist
            </button>
        </li>
    </ul>
    <!-- Indicatore di caricamento Livewire -->
    <div wire:loading class="text-center w-100 my-4">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Caricamento...</span>
        </div>
    </div>
    <!-- Griglia degli Anime -->
    <!-- Contenuto dinamico in base al filtro selezionato -->
    <div wire:loading.remove>
        @if($currentFilter === 'my-lists')
        <!-- Se siamo su Le mie Liste, carichiamo il gestore avanzato delle liste -->
        <livewire:manage-custom-lists />
        @else
        <!-- Altrimenti, mostriamo la classica griglia degli anime in corso/completati -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @forelse($animeList as $anime)
            <!-- Vecchio codice delle card anime... -->
            @empty
            <!-- Vecchio codice per lista vuota... -->
            @endforelse
        </div>
        @endif
    </div>
</div>