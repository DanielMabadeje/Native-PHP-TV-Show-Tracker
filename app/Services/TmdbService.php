<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TmdbService
{
    private string $base    = 'https://api.themoviedb.org/3';
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.key');
    }

    // Search for shows by name
    public function search(string $query): array
    {
        try {
            $response = Http::get("{$this->base}/search/tv", [
                'api_key' => $this->apiKey,
                'query'   => $query,
            ]);

            return $response->json('results', []);
        } catch (\Throwable $e) {
            Log::error('TMDB search failed: ' . $e->getMessage());
            return [];
        }
    }

    // Get full show details
    public function getShow(int $tmdbId): ?array
    {
        try {
            $response = Http::get("{$this->base}/tv/{$tmdbId}", [
                'api_key' => $this->apiKey,
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            Log::error('TMDB getShow failed: ' . $e->getMessage());
            return null;
        }
    }

    // Get all episodes for a specific season
    public function getSeason(int $tmdbId, int $season): array
    {
        try {
            $response = Http::get("{$this->base}/tv/{$tmdbId}/season/{$season}", [
                'api_key' => $this->apiKey,
            ]);

            return $response->json('episodes', []);
        } catch (\Throwable $e) {
            Log::error('TMDB getSeason failed: ' . $e->getMessage());
            return [];
        }
    }

    // Get the latest season number for a show
    public function getLatestSeasonNumber(int $tmdbId): ?int
    {
        $show = $this->getShow($tmdbId);
        if (! $show) return null;

        $seasons = collect($show['seasons'] ?? [])
            ->filter(fn($s) => $s['season_number'] > 0) // exclude specials
            ->sortByDesc('season_number');

        return $seasons->first()['season_number'] ?? null;
    }
}