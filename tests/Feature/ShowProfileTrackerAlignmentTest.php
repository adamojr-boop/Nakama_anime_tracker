<?php

use App\Livewire\Profile\ShowProfile;
use App\Models\AnimeMetadata;
use App\Models\EpisodeTracker;
use App\Models\User;
use Livewire\Livewire;

test('profile showcase displays watching anime from episode trackers', function () {
    $user = User::factory()->create();

    AnimeMetadata::create([
        'mal_id' => 959,
        'title' => 'Hunter x Hunter',
        'image_url' => 'https://example.test/hxh.jpg',
        'total_episodes' => 148,
    ]);

    EpisodeTracker::create([
        'user_id' => $user->id,
        'mal_id' => 959,
        'watched_episodes' => 3,
        'status' => 'watching',
        'episode_duration' => 24,
    ]);

    Livewire::test(ShowProfile::class, ['userId' => $user->id])
        ->assertSee('Hunter x Hunter')
        ->assertSee('Ep. 3 / 148');
});

test('profile stats are computed from episode trackers', function () {
    $user = User::factory()->create();

    EpisodeTracker::create([
        'user_id' => $user->id,
        'mal_id' => 100,
        'watched_episodes' => 5,
        'status' => 'completed',
        'episode_duration' => 24,
    ]);

    EpisodeTracker::create([
        'user_id' => $user->id,
        'mal_id' => 101,
        'watched_episodes' => 3,
        'status' => 'watching',
        'episode_duration' => 24,
    ]);

    Livewire::test(ShowProfile::class, ['userId' => $user->id])
        ->call('setTab', 'stats')
        ->assertSee('Anime Completati')
        ->assertSee('1')
        ->assertSee('Episodi Visti')
        ->assertSee('8')
        ->assertSee('Tempo Totale')
        ->assertSee('3h');
});
