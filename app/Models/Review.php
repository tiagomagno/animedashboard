<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'anime_id',
        'user_id',
        'score_story',
        'score_direction',
        'score_animation',
        'score_soundtrack',
        'score_impact',
        'final_score',
        'weights',
        'notes',
        'is_published',
    ];

    protected $casts = [
        'score_story' => 'decimal:1',
        'score_direction' => 'decimal:1',
        'score_animation' => 'decimal:1',
        'score_soundtrack' => 'decimal:1',
        'score_impact' => 'decimal:1',
        'final_score' => 'decimal:2',
        'weights' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * Get the anime that owns the review.
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    /**
     * Get the user that owns the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate and update the final score.
     */
    public function calculateFinalScore(): float
    {
        $scores = [
            'story' => $this->score_story,
            'direction' => $this->score_direction,
            'animation' => $this->score_animation,
            'soundtrack' => $this->score_soundtrack,
            'impact' => $this->score_impact,
        ];

        // Remove null values
        $scores = array_filter($scores, fn($score) => $score !== null);

        if (empty($scores)) {
            return 0;
        }

        // Check if we have custom weights
        if ($this->weights && is_array($this->weights)) {
            $totalWeight = 0;
            $weightedSum = 0;

            foreach ($scores as $criterion => $score) {
                $weight = $this->weights[$criterion] ?? 1;
                $weightedSum += $score * $weight;
                $totalWeight += $weight;
            }

            return $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : 0;
        }

        // Simple average
        return round(array_sum($scores) / count($scores), 2);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($review) {
            $review->final_score = $review->calculateFinalScore();
        });
    }

    /**
     * Scope to get published reviews.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get top rated reviews.
     */
    public function scopeTopRated($query, int $limit = 10)
    {
        return $query->whereNotNull('final_score')
            ->orderBy('final_score', 'desc')
            ->limit($limit);
    }
}
