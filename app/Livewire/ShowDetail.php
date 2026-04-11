<?php

namespace App\Livewire;

use App\Models\Episode;
use App\Models\Show;
use App\Services\EpisodeCheckerService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ShowDetail extends Component
{
    public Show $show;
    public string $status = '';

    public function mount(Show $show): void
    {
        $this->show = $show;
    }

    public function refresh(): void
    {
        $this->status = 'Checking for new episodes...';
        $count = app(EpisodeCheckerService::class)->checkShow($this->show);
        $this->show->refresh();
        $this->status = $count > 0
            ? "✅ {$count} new " . ($count === 1 ? 'episode' : 'episodes') . ' found!'
            : 'All up to date.';
    }

    public function markSeen(int $episodeId): void
    {
        Episode::findOrFail($episodeId)->update(['is_seen' => true, 'is_new' => false]);
        $this->show->refresh();
    }

    public function markAllSeen(): void
    {
        $this->show->episodes()->update(['is_seen' => true, 'is_new' => false]);
        $this->show->refresh();
    }

    public function render()
    {
        $episodes = $this->show->episodes()
            ->orderByDesc('season')
            ->orderByDesc('episode')
            ->get()
            ->groupBy('season');

        return view('livewire.show-detail', compact('episodes'));
    }
}