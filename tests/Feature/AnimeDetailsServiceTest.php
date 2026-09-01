<?php

use App\Services\AnimeDetailsService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('caches a successful Jikan anime detail response', function () {
    Cache::flush();
    Http::fake([
        'api.jikan.moe/*' => Http::response([
            'data' => ['mal_id' => 20, 'title' => 'Naruto'],
        ]),
    ]);

    $service = app(AnimeDetailsService::class);

    expect($service->getForMalId(20))->toMatchArray(['mal_id' => 20, 'title' => 'Naruto']);
    expect($service->getForMalId(20))->toMatchArray(['mal_id' => 20, 'title' => 'Naruto']);

    Http::assertSentCount(1);
});

it('preserves the requested MAL ID when Kitsu provides fallback metadata', function () {
    Cache::flush();
    Http::fake([
        'api.jikan.moe/*' => Http::response([], 503),
        'kitsu.io/*' => Http::response([
            'data' => [
                'id' => '98765',
                'attributes' => [
                    'canonicalTitle' => 'Fallback Anime',
                    'showType' => 'TV',
                ],
            ],
        ]),
    ]);

    $anime = app(AnimeDetailsService::class)->getForMalId(20);

    expect($anime)->toMatchArray(['mal_id' => 20, 'title' => 'Fallback Anime']);
});
