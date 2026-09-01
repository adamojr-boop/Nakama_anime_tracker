<?php

use App\Services\StreamingAvailabilityService;
use Illuminate\Support\Facades\Http;

it('caches Italian providers and a YouTube trailer from TMDb', function () {
    config()->set('services.tmdb.key', 'test-key');
    config()->set('services.tmdb.region', 'IT');

    Http::fake([
        'api.themoviedb.org/3/search/tv*' => Http::response(['results' => [['id' => 123]]]),
        'api.themoviedb.org/3/tv/123/watch/providers*' => Http::response([
            'results' => ['IT' => [
                'link' => 'https://www.justwatch.com/it/serie-tv/naruto',
                'flatrate' => [['provider_name' => 'Crunchyroll', 'logo_path' => '/crunchyroll.png']],
            ]],
        ]),
        'api.themoviedb.org/3/tv/123/videos*' => Http::response([
            'results' => [['site' => 'YouTube', 'type' => 'Trailer', 'key' => 'trailer-key']],
        ]),
    ]);

    $service = app(StreamingAvailabilityService::class);
    $result = $service->getForAnime(20, 'Naruto');

    expect($result['providers'])->toBe([['name' => 'Crunchyroll', 'logo_path' => '/crunchyroll.png']])
        ->and($result['trailer_url'])->toBe('https://www.youtube.com/watch?v=trailer-key');

    $service->getForAnime(20, 'Naruto');

    Http::assertSentCount(3);
});
