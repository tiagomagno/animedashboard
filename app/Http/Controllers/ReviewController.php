<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Show the review form.
     */
    public function create(Anime $anime)
    {
        $review = $anime->review;

        return view('reviews.create', compact('anime', 'review'));
    }

    /**
     * Store or update a review.
     */
    public function store(Request $request, Anime $anime)
    {
        $validated = $request->validate([
            'score_story' => 'nullable|numeric|min:0|max:10',
            'score_direction' => 'nullable|numeric|min:0|max:10',
            'score_animation' => 'nullable|numeric|min:0|max:10',
            'score_soundtrack' => 'nullable|numeric|min:0|max:10',
            'score_impact' => 'nullable|numeric|min:0|max:10',
            'notes' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id() ?? 1; // Default to user 1 for MVP
        $validated['anime_id'] = $anime->id;

        $review = Review::updateOrCreate(
            ['anime_id' => $anime->id, 'user_id' => $validated['user_id']],
            $validated
        );

        return redirect()
            ->route('dashboard.show', $anime)
            ->with('success', 'Review saved successfully!');
    }

    /**
     * Delete a review.
     */
    public function destroy(Anime $anime)
    {
        $anime->review?->delete();

        return redirect()
            ->route('dashboard.show', $anime)
            ->with('success', 'Review deleted successfully.');
    }
}
