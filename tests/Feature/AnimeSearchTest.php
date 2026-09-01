<?php

use App\Livewire\AnimeSearch;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('caches successful Jikan search results', function () {
    Cache::flush();
    Http::fake([
        'api.jikan.moe/*' => Http::response([
            'data' => [[
                'mal_id' => 20,
                'title' => 'Naruto',
                'type' => 'TV',
                'episodes' => 220,
                'score' => 7.9,
                'images' => ['jpg' => ['small_image_url' => 'https://example.test/naruto.jpg']],
            ]],
        ]),
    ]);

    Livewire::test(AnimeSearch::class)
        ->set('searchQuery', 'Naruto')
        ->assertSet('results.0.mal_id', 20);

    Livewire::test(AnimeSearch::class)
        ->set('searchQuery', 'Naruto')
        ->assertSet('results.0.mal_id', 20);

    Http::assertSentCount(1);
});