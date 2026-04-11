<?php

namespace App\Livewire;

use App\Models\Show;
use App\Services\TmdbService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ShowSearch extends Component
{
    public string $query   = '';
    public array $results  = [];
    public string $status  = '';
    public bool $searching = false;

    public function search(): void
    {
        if (strlen(trim($this->query)) < 2) return;

        $this->searching = true;
        $this->results   = [];

        try {
            $this->results = app(TmdbService::class)->search($this->query);
        } catch (\Throwable $e) {
            $this->status = 'Search failed. Check your connection.';
            Log::error('ShowSearch failed: ' . $e->getMessage());
        }

        $this->searching = false;
    }

    public function addShow(int $tmdbId): void
    {
        if (Show::where('tmdb_id', $tmdbId)->exists()) {
            $this->status = 'Already tracking this show.';
            return;
        }

        $data = app(TmdbService::class)->getShow($tmdbId);

        if (! $data) {
            $this->status = 'Could not load show details.';
            return;
        }

        Show::create([
            'tmdb_id'     => $tmdbId,
            'name'        => $data['name'],
            'overview'    => $data['overview'] ?? null,
            'poster_path' => $data['poster_path'] ?? null,
            'status'      => $data['status'] ?? null,
        ]);

        $this->status = "✅ Now tracking {$data['name']}!";
        $this->results = [];
        $this->query   = '';
    }

    public function render()
    {
        $tracked = Show::pluck('tmdb_id')->toArray();
        return view('livewire.show-search', compact('tracked'));
    }
}