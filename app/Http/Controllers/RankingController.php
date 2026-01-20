<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        // Trending & Popular - Animes ganhando mais seguidores (crescimento recente)
        $trending = Anime::with('season')
            ->whereNotNull('num_list_users')
            ->orderByDesc('num_list_users')
            ->take(10)
            ->get();

        // Top 10 MyAnimeList (maiores notas)
        $topMal = Anime::with('season')
            ->whereNotNull('mean')
            ->where('mean', '>', 0)
            ->orderByDesc('mean')
            ->orderByDesc('num_list_users')
            ->take(10)
            ->get();

        // Top 10 da Temporada Vigente
        $currentYear = now()->year;
        $currentSeason = $this->getCurrentSeason();
        
        $topSeason = Anime::with('season')
            ->whereHas('season', function($q) use ($currentYear, $currentSeason) {
                $q->where('year', $currentYear)
                  ->where('season', $currentSeason);
            })
            ->whereNotNull('mean')
            ->orderByDesc('mean')
            ->orderByDesc('num_list_users')
            ->take(10)
            ->get();

        // Available years for header
        $availableYears = range(date('Y'), 2015);

        return view('rankings.index', compact('trending', 'topMal', 'topSeason', 'currentYear', 'currentSeason', 'availableYears'));
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
