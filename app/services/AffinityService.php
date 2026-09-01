<?php

namespace App\Services;

use App\Models\User;
use App\Models\EpisodeRating;

class AffinityService
{
    /**
     * Calcola l'affinità percentuale (0% - 100%) tra l'utente target e l'utente autenticato/confrontato.
     */
    public function calculate(User $userA, User $userB): int
    {
        // Se si tratta dello stesso utente, affinità al 100%
        if ($userA->id === $userB->id) {
            return 100;
        }

        // 1. Calcolo affinità sui generi (peso: 60%)
        $genreScore = $this->calculateGenreAffinity($userA, $userB);

        // 2. Calcolo affinità sulle reazioni emotive (peso: 40%)
        $emotionScore = $this->calculateEmotionAffinity($userA, $userB);

        // Se non ci sono dati sufficienti per entrambi i fattori, restituiamo 0
        if ($genreScore === null && $emotionScore === null) {
            return 0;
        }

        // Se uno dei due fattori manca di dati, ponderiamo sull'altro
        if ($genreScore === null) return round($emotionScore);
        if ($emotionScore === null) return round($genreScore);

        // Peso combinato: 60% generi, 40% reazioni emotive
        $finalScore = ($genreScore * 0.60) + ($emotionScore * 0.40);

        return (int) round(min(100, max(0, $finalScore)));
    }

    /**
     * Confronta le reazioni emotive condivise (stesso anime e stesso episodio)
     */
    private function calculateEmotionAffinity(User $userA, User $userB): ?float
    {
        // Recuperiamo i rating in cui entrambi gli utenti hanno espresso un'emozione nello stesso episodio
        $ratingsA = EpisodeRating::where('user_id', $userA->id)
            ->whereNotNull('emotion')
            ->get(['anime_mal_id', 'episode_number', 'emotion'])
            ->keyBy(fn($item) => $item->anime_mal_id . '_' . $item->episode_number);

        if ($ratingsA->isEmpty()) {
            return null;
        }

        $ratingsB = EpisodeRating::where('user_id', $userB->id)
            ->whereNotNull('emotion')
            ->get(['anime_mal_id', 'episode_number', 'emotion'])
            ->keyBy(fn($item) => $item->anime_mal_id . '_' . $item->episode_number);

        if ($ratingsB->isEmpty()) {
            return null;
        }

        // Troviamo gli episodi valutati da entrambi
        $commonKeys = $ratingsA->keys()->intersect($ratingsB->keys());

        if ($commonKeys->isEmpty()) {
            return null;
        }

        $matches = 0;
        foreach ($commonKeys as $key) {
            if ($ratingsA[$key]->emotion === $ratingsB[$key]->emotion) {
                $matches++;
            }
        }

        return ($matches / $commonKeys->count()) * 100;
    }

    /**
     * Confronta i generi anime preferiti dei due utenti
     */
    private function calculateGenreAffinity(User $userA, User $userB): ?float
    {
        $genresA = $this->trackedGenres($userA);
        $genresB = $this->trackedGenres($userB);

        if (empty($genresA) || empty($genresB)) {
            return null;
        }

        $intersection = array_intersect($genresA, $genresB);
        $union = array_unique(array_merge($genresA, $genresB));

        if (count($union) === 0) {
            return null;
        }

        // Jaccard similarity index
        return (count($intersection) / count($union)) * 100;
    }

    private function trackedGenres(User $user): array
    {
        return $user->episodeTrackers()
            ->with('animeMetadata:mal_id,genres')
            ->get()
            ->flatMap(fn ($tracker) => $tracker->animeMetadata?->genres ?? [])
            ->filter(fn ($genre) => is_string($genre) && $genre !== '')
            ->map(fn ($genre) => mb_strtolower(trim($genre)))
            ->unique()
            ->values()
            ->all();
    }
}
