<?php

use App\Livewire\AnimeRecommendationModal;
use App\Models\AnimeMetadata;
use App\Models\EpisodeTracker;
use App\Models\User;
use Livewire\Livewire;

test('it generates a recommendation from episode trackers for watching status', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

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

    Livewire::test(AnimeRecommendationModal::class)
        ->set('availableTime', 30)
        ->call('generateRecommendation')
        ->assertSet('errorMessage', null)
        ->assertSet('recommendation.status', 'watching')
        ->assertSet('recommendation.title', 'Hunter x Hunter')
        ->assertSet('recommendation.start_episode', 4);
});

test('it can include plan to watch anime when toggle is enabled', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    AnimeMetadata::create([
        'mal_id' => 7008,
        'title' => 'Attack on Titan',
        'image_url' => 'https://example.test/aot.jpg',
        'total_episodes' => 25,
    ]);

    EpisodeTracker::create([
        'user_id' => $user->id,
        'mal_id' => 7008,
        'watched_episodes' => 0,
        'status' => 'plan_to_watch',
        'episode_duration' => 24,
    ]);

    Livewire::test(AnimeRecommendationModal::class)
        ->set('availableTime', 30)
        ->set('includePlanToWatch', true)
        ->call('generateRecommendation')
        ->assertSet('errorMessage', null)
        ->assertSet('recommendation.status', 'plan_to_watch')
        ->assertSet('recommendation.title', 'Attack on Titan')
        ->assertSet('recommendation.start_episode', 1);
});
