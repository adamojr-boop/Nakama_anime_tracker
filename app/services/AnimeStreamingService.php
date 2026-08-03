<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AnimeStreamingService
{
    /**
     * Recupera i provider/link di streaming ufficiali per un anime dato il suo ID MyAnimeList o Titolo.
     */
    public function getStreamingPlatforms(int|string $animeIdentifier): array
    {
        $cacheKey = 'anime_streaming_v2_' . md5($animeIdentifier);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($animeIdentifier) {
            // Se abbiamo un ID numerico (MAL ID), interroghiamo direttamente l'endpoint di Jikan
            if (is_numeric($animeIdentifier)) {
                return $this->fetchFromJikanById((int) $animeIdentifier);
            }

            // Altrimenti cerchiamo per titolo
            return $this->fetchFromJikanByTitle((string) $animeIdentifier);
        });
    }

    private function fetchFromJikanById(int $malId): array
    {
        $response = Http::get("https://api.jikan.moe/v4/anime/{$malId}/streaming");

        if ($response->failed()) {
            return [];
        }

        $data = $response->json('data') ?? [];

        return $this->formatStreamingData($data);
    }

    private function fetchFromJikanByTitle(string $title): array
    {
        // 1. Cerca l'anime su Jikan
        $searchResponse = Http::get("https://api.jikan.moe/v4/anime", [
            'q' => $title,
            'limit' => 1
        ]);

        $firstResult = $searchResponse->json('data.0');

        if (!$firstResult) {
            return [];
        }

        $malId = $firstResult['mal_id'];

        return $this->fetchFromJikanById($malId);
    }

    private function formatStreamingData(array $streamingList): array
    {
        $formatted = [];

        foreach ($streamingList as $item) {
            $name = $item['name'] ?? '';
            $url = $item['url'] ?? '#';

            $formatted[] = [
                'name' => $name,
                'url' => $url,
                'icon' => $this->detectPlatformIcon($name),
                'badge_color' => $this->detectPlatformColor($name),
            ];
        }

        return $formatted;
    }

    private function detectPlatformIcon(string $name): string
    {
        $nameLower = strtolower($name);

        return match (true) {
            str_contains($nameLower, 'crunchyroll') => '🟠',
            str_contains($nameLower, 'netflix')     => '🔴',
            str_contains($nameLower, 'prime')       => '🔵',
            str_contains($nameLower, 'disney')      => '🌌',
            str_contains($nameLower, 'hulu')        => '🟢',
            str_contains($nameLower, 'bilibili')    => '📺',
            default                                 => '🎬',
        };
    }

    private function detectPlatformColor(string $name): string
    {
        $nameLower = strtolower($name);

        return match (true) {
            str_contains($nameLower, 'crunchyroll') => 'bg-warning text-dark',
            str_contains($nameLower, 'netflix')     => 'bg-danger text-white',
            str_contains($nameLower, 'prime')       => 'bg-primary text-white',
            str_contains($nameLower, 'disney')      => 'bg-info text-dark',
            default                                 => 'bg-secondary text-white',
        };
    }
}
