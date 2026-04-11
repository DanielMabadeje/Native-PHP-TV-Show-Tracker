<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $fillable = [
        'show_id',
        'season',
        'episode',
        'name',
        'air_date',
        'overview',
        'is_new',
        'is_seen',
    ];

    protected $casts = [
        'air_date' => 'date',
        'is_new'   => 'boolean',
        'is_seen'  => 'boolean',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(Show::class);
    }

    public function label(): string
    {
        return "S{$this->season}E{$this->episode}";
    }
}