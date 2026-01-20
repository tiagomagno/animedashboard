<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\Anime;
use Illuminate\Http\Request;
use App\Services\SeasonImportService;

class DashboardController extends Controller
{
    public function __construct(protected SeasonImportService $importService) {}

    /**
     * Display the main dashboard.
     */
    public function index(Request $request)
    {
        // Auto-detect current season logic on first load
        if (!$request->has('year')) {
            $currentYear = now()->year;
            $currentSeason = $this->getCurrentSeason();

            // Check if we have data for current season
            $exists = Season::where('year', $currentYear)
                ->where('season', $currentSeason)
                ->exists();

            if (!$exists) {
                // First time load: Import automatically
                try {
                    $this->importService->importSeason($currentYear, $currentSeason);
                } catch (\Exception $e) {
                    // Log but continue
                }
            }
            
            // Redirect to apply params with defaults
            return redirect()->route('dashboard.index', [
                'year' => $currentYear, 
                'season' => $currentSeason,
                'hide_kids' => $request->get('hide_kids', '1'), // Default hidden
                'hide_adult' => $request->get('hide_adult', '1'), // Default hidden
            ]);
        }

        // Get params
        $year = $request->get('year');
        $seasonName = $request->get('season', $this->getCurrentSeason());
        $mediaType = $request->get('type', 'all');
        
        // Logic: If checked (1), we HIDE content. Default is 0 (Show).
        // User request: "Os filtros ativos... são para ocultar"
        $hideKids = $request->get('hide_kids', '0') === '1';
        $hideAdult = $request->get('hide_adult', '0') === '1';

        // Get available years
        $availableYears = Season::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Check availability
        if ($availableYears->isEmpty() && !$request->has('year')) {
             return view('dashboard.empty');
        }

        // Get seasons for this year (for tabs stats)
        $seasons = Season::where('year', $year)
            ->orderByRaw("
                CASE season
                    WHEN 'winter' THEN 1
                    WHEN 'spring' THEN 2
                    WHEN 'summer' THEN 3
                    WHEN 'fall' THEN 4
                END
            ")
            ->get();

        // Base Query for Stats (Unfiltered by season type for tabs count)
        $baseQuery = Anime::query()
            ->whereHas('season', function($q) use ($year) {
                $q->where('year', $year);
            });

        // Apply same filters as main query to ensure tabs count matches visible reality
        if ($mediaType !== 'all') {
            $baseQuery->where('media_type', $mediaType);
        }
        if ($hideKids) {
            $baseQuery->where(function($q) {
                $q->whereNotIn('rating', ['g', 'pg'])->orWhereNull('rating');
            });
        }
        if ($hideAdult) {
            $baseQuery->where(function($q) {
                $q->whereNotIn('rating', ['r+', 'rx'])->orWhereNull('rating');
            });
        }
            
        // Calculate season counts
        $statsBySeason = [];
        $allSeasonsAnimes = $baseQuery->get();
        $totalYearCount = $allSeasonsAnimes->count();
        
        foreach (['winter', 'spring', 'summer', 'fall'] as $s) {
            $seasonId = $seasons->where('season', $s)->first()?->id;
            $count = $seasonId ? $allSeasonsAnimes->where('season_id', $seasonId)->count() : 0;
            $statsBySeason[$s] = ['total' => $count];
        }

        // Now Apply Season Filter for Main Grid
        $query = Anime::query()
            ->with(['season', 'review']) // Eager load review
            ->whereHas('season', function($q) use ($year, $seasonName) {
                $q->where('year', $year);
                if ($seasonName !== 'all') {
                    $q->where('season', $seasonName);
                }
            });

        // Apply media type filter
        if ($mediaType !== 'all') {
            $query->where('media_type', $mediaType);
        }

        // Apply rating filters (Hiding logic)
        // Rating field values: g, pg, pg_13, r, r+, rx
        // Kids: g, pg
        // Adult: r+, rx
        if ($hideKids) {
            $query->where(function($q) {
                $q->whereNotIn('rating', ['g', 'pg'])
                  ->orWhereNull('rating');
            });
        }
        if ($hideAdult) {
            $query->where(function($q) {
                $q->whereNotIn('rating', ['r+', 'rx'])
                  ->orWhereNull('rating');
            });
        }

        // Sorting Logic
        $ranking = $request->get('ranking');
        $isRankingView = false;

        switch ($ranking) {
            case 'score':
                $query->orderByDesc('mean');
                $isRankingView = true;
                break;
            case 'popularity':
                $query->orderBy('popularity'); // Lower is better
                $isRankingView = true;
                break;
            case 'members':
                $query->orderByDesc('num_list_users');
                $isRankingView = true;
                break;
            case 'editorial':
                $query->select('animes.*')
                    ->join('reviews', 'animes.id', '=', 'reviews.anime_id')
                    ->orderByDesc('reviews.final_score');
                $isRankingView = true;
                break;
            case 'recent':
                $query->orderByDesc('start_date');
                $isRankingView = true;
                break;
            default:
                 // Default sort: Score or Members
                 $query->orderByDesc('mean')->orderByDesc('num_list_users');
        }

        if ($isRankingView) {
            $query->take(100);
        }

        $animes = $query->get();

        // Stats for current view
        $stats = [
            'total_animes' => $animes->count(),
            'reviewed_animes' => $animes->filter(fn($a) => $a->review)->count(),
            'avg_mal_score' => $animes->whereNotNull('mean')->avg('mean'),
            'avg_editorial_score' => $animes->filter(fn($a) => $a->review)
                ->avg(fn($a) => $a->review->final_score),
        ];

        // Top Lists (Ideally cached or separate API call, but calculating here for now)
        // Using visible animes to respect filters
        $topByMalScore = $animes->sortByDesc('mean')->take(10);
        $topByPopularity = $animes->sortBy('popularity')->take(10);
        $topByMembers = $animes->sortByDesc('num_list_users')->take(10);
        
        $mediaTypes = [
            'all' => 'All',
            'tv' => 'TV',
            'ona' => 'ONA',
            'ova' => 'OVA',
            'movie' => 'Movie',
            'special' => 'Special',
        ];

        return view('dashboard.index', compact(
            'year',
            'seasonName',
            'mediaType',
            'hideKids',
            'hideAdult',
            'availableYears',
            'seasons',
            'animes',
            'stats',
            'statsBySeason',
            'totalYearCount',
            'topByMalScore',
            'topByPopularity',
            'topByMembers',
            'mediaTypes',
            'isRankingView'
        ));
    }

    public function show(Anime $anime)
    {
        $anime->load(['season', 'review', 'stats' => function ($query) {
            $query->orderBy('recorded_at', 'asc');
        }]);

        $chartData = [
            'labels' => $anime->stats->pluck('recorded_at')->map(fn($date) => $date->format('M d'))->toArray(),
            'score' => $anime->stats->pluck('mean')->toArray(),
            'members' => $anime->stats->pluck('num_list_users')->toArray(),
        ];

        return view('dashboard.show', compact('anime', 'chartData'));
    }

    private function getCurrentSeason(): string
    {
        $month = now()->month;
        if ($month >= 1 && $month <= 3) return 'winter';
        if ($month >= 4 && $month <= 6) return 'spring';
        if ($month >= 7 && $month <= 9) return 'summer';
        return 'fall';
    }
}
