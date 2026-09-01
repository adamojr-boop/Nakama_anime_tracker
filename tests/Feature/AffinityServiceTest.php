<?php

use App\Models\AnimeMetadata;
use App\Models\EpisodeTracker;
use App\Models\User;
use App\Services\AffinityService;

it('calculates full affinity for users with the same tracked genres', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    AnimeMetadata::create([
        'mal_id' => 20,
        'title' => 'Naruto',
        'genres' => ['Action', 'Adventure'],
    ]);

    foreach ([$firstUser, $secondUser] as $user) {
        EpisodeTracker::create([
            'user_id' => $user->id,
            'mal_id' => 20,
            'watched_episodes' => 1,
            'status' => 'watching',
        ]);
    }

    expect(app(AffinityService::class)->calculate($firstUser, $secondUser))->toBe(100);
});

it('returns zero affinity when users have no comparable data', function () {
    expect(app(AffinityService::class)->calculate(User::factory()->create(), User::factory()->create()))->toBe(0);
});
