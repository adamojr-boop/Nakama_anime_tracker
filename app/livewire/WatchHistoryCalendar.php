<?php

namespace App\Livewire;

use App\Models\EpisodeLog;
use App\Services\AnimeMetadataService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On; // <-- Importato correttamente qui

class WatchHistoryCalendar extends Component
{
    public string $currentMonth; // Formato YYYY-MM
    public ?array $selectedDayLogs = null;
    public ?string $selectedDateFormatted = null;

    public function mount()
    {
        $this->currentMonth = now()->format('Y-m');
    }

    public function previousMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->subMonth()->format('Y-m');
        $this->selectedDayLogs = null;
    }

    public function nextMonth()
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->addMonth()->format('Y-m');
        $this->selectedDayLogs = null;
    }

    public function selectDay(string $dateStr, array $logs)
    {
        $this->selectedDateFormatted = Carbon::parse($dateStr)->translatedFormat('d F Y');
        $this->selectedDayLogs = $logs;
    }

    public function closeDayDetails()
    {
        $this->selectedDayLogs = null;
    }

    /**
     * Listener: si attiva quando viene spuntato un episodio altrove
     */
    #[On('episode-logged')]
    public function refreshCalendar()
    {
        // Rinfresca il componente per mostrare subito la nuova visione sul calendario
    }

    public function render()
    {
        $userId = auth()->id();
        $date = Carbon::parse($this->currentMonth . '-01');

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // 1. Recupera i log del mese per l'utente loggato
        $logs = EpisodeLog::query()
            ->where('user_id', $userId)
            ->whereBetween('watched_at', [$startOfMonth->startOfDay(), $endOfMonth->endOfDay()])
            ->orderBy('watched_at', 'asc')
            ->get();

        // 2. Recupera i metadati (titolo, immagine) per tutti gli anime nei log
        $malIds = $logs->pluck('mal_id')->unique()->all();
        $metadata = app(AnimeMetadataService::class)
            ->getForMalIds($malIds)
            ->keyBy('mal_id');

        // 3. Raggruppa i log per data (YYYY-MM-DD) arricchendoli con i metadati
        $groupedLogs = [];
        foreach ($logs as $log) {
            $dayKey = $log->watched_at->format('Y-m-d');
            $meta = $metadata->get((int) $log->mal_id);

            $groupedLogs[$dayKey][] = [
                'id' => $log->id,
                'mal_id' => $log->mal_id,
                'episode_number' => $log->episode_number,
                'watched_time' => $log->watched_at->format('H:i'),
                'title' => $meta?->title ?? "Anime #{$log->mal_id}",
                'image_url' => $meta?->image_url,
            ];
        }

        // 4. Costruzione della griglia del calendario
        $calendarGrid = [];
        $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 (Lunedì) -> 7 (Domenica)

        // Giorni vuoti del mese precedente
        for ($i = 1; $i < $startDayOfWeek; $i++) {
            $calendarGrid[] = null;
        }

        // Giorni del mese corrente
        for ($day = 1; $day <= $date->daysInMonth; $day++) {
            $dayDate = $date->copy()->day($day)->format('Y-m-d');
            $calendarGrid[] = [
                'day' => $day,
                'date' => $dayDate,
                'is_today' => $dayDate === now()->format('Y-m-d'),
                'logs' => $groupedLogs[$dayDate] ?? [],
            ];
        }

        return view('anime.watch-history-calendar', [
            'monthTitle' => $date->translatedFormat('F Y'),
            'calendarGrid' => $calendarGrid,
        ]);
    }
}
