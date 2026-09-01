<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AniListService
{
    protected string $endpoint = 'https://graphql.anilist.co';

    /**
     * Recupera la programmazione dei prossimi episodi in uscita per gli anime correnti.
     */
    public function getUpcomingAiringSchedules(int $page = 1, int $perPage = 50): array
    {
        $query = '
        query ($page: Int, $perPage: Int, $airingAtGreater: Int) {
            Page(page: $page, perPage: $perPage) {
                airingSchedules(airingAt_greater: $airingAtGreater, sort: TIME) {
                    id
                    airingAt
                    episode
                    media {
                        id
                        idMal
                        title {
                            romaji
                            english
                        }
                        coverImage {
                            large
                        }
                    }
                }
            }
        }
        ';

        $variables = [
            'page' => $page,
            'perPage' => $perPage,
            'airingAtGreater' => Carbon::now()->timestamp, // Solamente gli episodi da adesso in poi
        ];

        $response = Http::timeout(8)
            ->connectTimeout(3)
            ->retry(2, 500)
            ->post($this->endpoint, [
                'query' => $query,
                'variables' => $variables,
            ]);

        if ($response->successful()) {
            return $response->json('data.Page.airingSchedules') ?? [];
        }

        return [];
    }
}
