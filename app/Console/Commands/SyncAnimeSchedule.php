<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AniListService;
use App\Models\Episode;
use Carbon\Carbon;

class SyncAnimeSchedule extends Command
{
    protected $signature = 'anime:sync-schedule';
    protected $description = 'Sincronizza i prossimi episodi in uscita da AniList nel DB locale';

    public function handle(AniListService $aniListService)
    {
        $this->info('Inizio sincronizzazione palinsesto da AniList...');

        $schedules = $aniListService->getUpcomingAiringSchedules(1, 50);

        if (empty($schedules)) {
            $this->warn('Nessun episodio trovato o errore durante la chiamata API.');
            return;
        }

        $count = 0;

        foreach ($schedules as $item) {
            $media = $item['media'];
            $animeTitle = $media['title']['romaji'] ?? $media['title']['english'] ?? 'Anime Sconosciuto';
            $episodeNum = $item['episode'];

            // Titolo completo da mostrare: "Nome Anime - Episodio X"
            $fullTitle = "{$animeTitle}";

            $airDateUtc = Carbon::createFromTimestamp($item['airingAt'])->utc();

            Episode::updateOrCreate(
                [
                    'anime_id' => $media['id'],
                    'episode_number' => $episodeNum,
                ],
                [
                    'title' => $fullTitle,
                    'air_date_utc' => $airDateUtc->toDateTimeString(),
                    'status' => 'scheduled',
                ]
            );
        }

        $this->info("Sincronizzazione completata! Aggiornati/Inseriti {$count} episodi.");
    }
}
