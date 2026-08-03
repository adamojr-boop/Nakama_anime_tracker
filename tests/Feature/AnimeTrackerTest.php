<?php

use App\Livewire\AnimeTracker;
use App\Models\EpisodeTracker;
use App\Models\User;
use Livewire\Livewire;

test('changing the status updates the component state and persists it', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(AnimeTracker::class, ['malId' => 123, 'totalEpisodes' => 12])
        ->set('watchedEpisodesList', [1, 2])
        ->call('changeStatus', 'on_hold')
        ->assertSet('currentStatus', 'on_hold')
        ->assertSet('watchedEpisodesList', [1, 2]);

    $tracker = EpisodeTracker::where('user_id', $user->id)
        ->where('mal_id', 123)
        ->first();

    expect($tracker)->not->toBeNull()
        ->and($tracker->status)->toBe('on_hold');
});

test('marking all episodes as watched switches the component to completed', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(AnimeTracker::class, ['malId' => 456, 'totalEpisodes' => 3])
        ->call('toggleEpisode', 1)
        ->call('toggleEpisode', 2)
        ->call('toggleEpisode', 3)
        ->assertSet('currentStatus', 'completed')
        ->assertSet('watchedEpisodesList', [1, 2, 3]);
});
