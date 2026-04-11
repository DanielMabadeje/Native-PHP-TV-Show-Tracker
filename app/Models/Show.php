<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model
{
    protected $fillable = [
        'tmdb_id',
        'name',
        'overview',
        'poster_path',
        'status',
        'last_checked_at',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function newEpisodes(): HasMany
    {
        return $this->hasMany(Episode::class)->where('is_new', true)->where('is_seen', false);
    }

    public function latestEpisode(): ?Episode
    {
        return $this->episodes()
            ->whereNotNull('air_date')
            ->orderByDesc('air_date')
            ->orderByDesc('season')
            ->orderByDesc('episode')
            ->first();
    }

    public function posterUrl(): string
    {
        if ($this->poster_path) {
            return 'https://image.tmdb.org/t/p/w300' . $this->poster_path;
        }
        return '';
    }
}