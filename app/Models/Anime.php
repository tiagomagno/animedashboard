<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anime extends Model
{
    use HasFactory;

    protected $fillable = [
        'mal_id',
        'season_id',
        'title',
        'alternative_titles',
        'synopsis',
        'main_picture_medium',
        'main_picture_large',
        'mean',
        'num_list_users',
        'popularity',
        'rank',
        'rating',
        'status',
        'media_type',
        'num_episodes',
        'genres',
        'start_date',
        'studios',
        'studios',
        'source',
        'broadcast',
        'related_animes',
        'last_updated_at',
    ];

    protected $casts = [
        'genres' => 'array',
        'studios' => 'array',
        'start_date' => 'date',
        'broadcast' => 'array',
        'related_animes' => 'array',
        'alternative_titles' => 'array',
        'mean' => 'decimal:2',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the season that owns the anime.
     */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * Get the stats history for the anime.
     */
    public function stats(): HasMany
    {
        return $this->hasMany(AnimeStat::class);
    }

    /**
     * Get the review for the anime.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Get all reviews for the anime.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the main picture URL.
     */
    public function getMainPictureAttribute(): ?string
    {
        return $this->main_picture_large ?? $this->main_picture_medium;
    }

    /**
     * Get formatted genres.
     */
    public function getGenresListAttribute(): string
    {
        if (!$this->genres || !is_array($this->genres)) {
            return 'N/A';
        }

        return collect($this->genres)
            ->pluck('name')
            ->implode(', ');
    }

    /**
     * Check if anime needs update (older than 24 hours).
     */
    public function needsUpdate(): bool
    {
        if (!$this->last_updated_at) {
            return true;
        }

        return $this->last_updated_at->diffInHours(now()) >= 24;
    }

    /**
     * Scope to get animes by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get top rated animes.
     */
    public function scopeTopRated($query, int $limit = 10)
    {
        return $query->whereNotNull('mean')
            ->orderBy('mean', 'desc')
            ->limit($limit);
    }

    /**
     * Scope to get most popular animes.
     */
    public function scopeMostPopular($query, int $limit = 10)
    {
        return $query->whereNotNull('popularity')
            ->orderBy('popularity', 'asc') // Lower is better in MAL
            ->limit($limit);
    }

    /**
     * Scope to get animes with reviews.
     */
    public function scopeWithReviews($query)
    {
        return $query->has('review');
    }
}
