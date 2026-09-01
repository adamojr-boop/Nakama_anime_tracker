<?php

namespace App\Livewire;

use App\Services\StreamingAvailabilityService;
use Livewire\Component;

class AnimeStreamingProviders extends Component
{
    public int $malId;
    public string $title;
    public array $providers = [];
    public ?string $trailerUrl = null;
    public ?string $watchUrl = null;

    public function mount(int $malId, string $title): void
    {
        $this->malId = $malId;
        $this->title = $title;

        $availability = app(StreamingAvailabilityService::class)->getForAnime($malId, $title);
        $this->providers = collect($availability['providers'])
            ->map(function (array $provider) {
                $url = $this->providerSearchUrl($provider['name']);

                return $url ? [...$provider, 'url' => $url] : null;
            })
            ->filter()
            ->unique('url')
            ->values()
            ->all();
        $this->trailerUrl = $availability['trailer_url'];
        $this->watchUrl = $availability['watch_url'];
    }

    private function providerSearchUrl(string $provider): ?string
    {
        $query = rawurlencode($this->title);

        return match ($provider) {
            'Amazon Prime Video', 'Amazon Prime Video with Ads' => "https://www.primevideo.com/search/ref=atv_nb_sr?phrase={$query}",
            'Crunchyroll' => "https://www.crunchyroll.com/search?q={$query}",
            'Netflix' => "https://www.netflix.com/search?q={$query}",
            'Disney Plus' => "https://www.disneyplus.com/search/{$query}",
            default => null,
        };
    }

    public function render()
    {
        return view('livewire.anime-streaming-providers');
    }
}