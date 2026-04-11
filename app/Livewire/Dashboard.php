<?php

namespace App\Livewire;

use App\Models\Show;
use App\Services\EpisodeCheckerService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Native\Mobile\Facades\Dialog;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public string $status   = '';
    public bool $checking   = false;
    public int $newCount    = 0;

    public function mount(): void
    {
        $this->checkForNewEpisodes();
    }

    public function testNotification(): void
    {
        Dialog::toast('🎉 New episode of Breaking Bad is out! S5E1', 'long');
    }

    public function checkForNewEpisodes(): void
    {
        $this->checking = true;

        try {
            $checker        = app(EpisodeCheckerService::class);
            $this->newCount = $checker->checkAll();

            if ($this->newCount > 0) {
                Dialog::toast(
                    "🎉 {$this->newCount} new " . ($this->newCount === 1 ? 'episode' : 'episodes') . ' available!',
                    'long'
                );
            }
        } catch (\Throwable $e) {
            Log::error('checkForNewEpisodes failed: ' . $e->getMessage());
        }

        $this->checking = false;
    }

    public function markAllSeen(int $showId): void
    {
        Show::findOrFail($showId)
            ->episodes()
            ->where('is_new', true)
            ->update(['is_new' => false, 'is_seen' => true]);
    }

    public function removeShow(int $showId): void
    {
        Show::findOrFail($showId)->delete();
    }

    public function render()
    {
        $shows = Show::with(['episodes' => fn($q) => $q->where('is_new', true)->where('is_seen', false)])
            ->latest()
            ->get();

        return view('livewire.dashboard', compact('shows'));
    }
}