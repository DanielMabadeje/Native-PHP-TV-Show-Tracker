<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Show;
use Illuminate\Support\Facades\Log;

class EpisodeCheckerService
{
    public function __construct(private TmdbService $tmdb) {}

    // Check all tracked shows for new episodes
    // Called on app boot — runs at most once per hour per show
    public function checkAll(): int
    {
        $newCount = 0;

        foreach (Show::all() as $show) {
            if ($this->shouldCheck($show)) {
                $newCount += $this->checkShow($show);
            }
        }

        return $newCount;
    }

    // Only check if we haven't checked in the last hour
    private function shouldCheck(Show $show): bool
    {
        if (! $show->last_checked_at) return true;
        return $show->last_checked_at->diffInHours(now()) >= 1;
    }

    // Check a single show for new episodes, returns count of new episodes found
    public function checkShow(Show $show): int
    {
        $newCount      = 0;
        $latestSeason  = $this->tmdb->getLatestSeasonNumber($show->tmdb_id);

        if (! $latestSeason) {
            $show->update(['last_checked_at' => now()]);
            return 0;
        }

        // Check the latest season and the one before (in case of split seasons)
        $seasonsToCheck = array_filter([$latestSeason - 1, $latestSeason], fn($s) => $s > 0);

        foreach ($seasonsToCheck as $seasonNum) {
            $episodes = $this->tmdb->getSeason($show->tmdb_id, $seasonNum);

            foreach ($episodes as $ep) {
                if (empty($ep['air_date'])) continue;

                // Skip episodes that haven't aired yet
                if (now()->lt(\Carbon\Carbon::parse($ep['air_date']))) continue;

                $existing = Episode::where('show_id', $show->id)
                    ->where('season', $seasonNum)
                    ->where('episode', $ep['episode_number'])
                    ->first();

                if (! $existing) {
                    Episode::create([
                        'show_id'  => $show->id,
                        'season'   => $seasonNum,
                        'episode'  => $ep['episode_number'],
                        'name'     => $ep['name'] ?? 'Episode ' . $ep['episode_number'],
                        'air_date' => $ep['air_date'],
                        'overview' => $ep['overview'] ?? null,
                        'is_new'   => true,
                        'is_seen'  => false,
                    ]);
                    $newCount++;
                    Log::info("New episode found: {$show->name} S{$seasonNum}E{$ep['episode_number']}");
                }
            }
        }

        $show->update(['last_checked_at' => now()]);

        return $newCount;
    }
}