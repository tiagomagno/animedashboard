<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'season',
        'name',
        'description',
        'is_active',
        'imported_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'imported_at' => 'datetime',
    ];

    /**
     * Get the animes for this season.
     */
    public function animes(): HasMany
    {
        return $this->hasMany(Anime::class);
    }

    /**
     * Get the display name for the season.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? ucfirst($this->season) . ' ' . $this->year;
    }

    /**
     * Scope to get active seasons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get seasons ordered by date (newest first).
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('year', 'desc')->orderByRaw("
            CASE season
                WHEN 'fall' THEN 1
                WHEN 'summer' THEN 2
                WHEN 'spring' THEN 3
                WHEN 'winter' THEN 4
            END
        ");
    }
}
