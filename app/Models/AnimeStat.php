<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimeStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'anime_id',
        'mean',
        'num_list_users',
        'popularity',
        'rank',
        'recorded_at',
    ];

    protected $casts = [
        'mean' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the anime that owns the stat.
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    /**
     * Scope to get stats for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get latest stats.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('recorded_at', 'desc');
    }
}
