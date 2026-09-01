<?php

use App\Livewire\AnimeReviews;
use App\Models\Review;
use App\Models\User;
use Livewire\Livewire;

it('persists the spoiler flag when an authenticated user submits a review', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(AnimeReviews::class, ['animeId' => 20])
        ->set('comment', 'Questo commento contiene uno spoiler.')
        ->set('rating', 8)
        ->set('isSpoiler', true)
        ->call('saveReview');

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'mal_id' => 20,
        'rating' => 8,
        'is_spoiler' => true,
    ]);
});

it('removes the spoiler overlay after a review is revealed', function () {
    $user = User::factory()->create();
    $review = Review::create([
        'user_id' => $user->id,
        'mal_id' => 20,
        'comment' => 'Questo commento contiene uno spoiler.',
        'rating' => 8,
        'is_spoiler' => true,
    ]);

    Livewire::test(AnimeReviews::class, ['animeId' => 20])
        ->assertSee('Clicca per mostrare lo spoiler')
        ->call('revealReview', $review->id)
        ->assertSet('revealedReviewIds', [$review->id])
        ->assertDontSee('Clicca per mostrare lo spoiler');
});
