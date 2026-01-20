<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anime;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $season = $request->get('season', $this->getCurrentSeason());
        
        // Filtros (Default Hidden = 1)
        $hideKids = $request->get('hide_kids', '1') === '1';
        $hideAdult = $request->get('hide_adult', '1') === '1';

        // Buscar animes da temporada
        $query = Anime::whereHas('season', function($q) use ($year, $season) {
                $q->where('year', $year)->where('season', $season);
            })
            ->whereNotNull('broadcast');

        // Aplicar Filtros
        if ($hideKids) {
            $query->where(function($q) {
                $q->whereNotIn('rating', ['g', 'pg'])->orWhereNull('rating');
            });
        }
        if ($hideAdult) {
            $query->where(function($q) {
                $q->whereNotIn('rating', ['r+', 'rx'])->orWhereNull('rating');
            });
        }

        // Filtro de Tipo de Mídia: Apenas TV, Especial e ONA (Ignora Filmes, OVAs)
        $query->whereIn('media_type', ['tv', 'special', 'ona']);

        $animes = $query->get();

        // Inicializar dias na ordem correta
        $calendar = [
            'monday' => [], 
            'tuesday' => [], 
            'wednesday' => [], 
            'thursday' => [], 
            'friday' => [], 
            'saturday' => [], 
            'sunday' => []
        ];

        foreach($animes as $anime) {
            // O broadcast vem como array devido ao cast no Model
            $day = isset($anime->broadcast['day_of_the_week']) 
                ? strtolower($anime->broadcast['day_of_the_week']) 
                : null;

            if ($day && isset($calendar[$day])) {
                $calendar[$day][] = $anime;
            }
        }

        return view('calendar.index', compact('calendar', 'year', 'season', 'hideKids', 'hideAdult'));
    }

    private function getCurrentSeason()
    {
        $month = now()->month;
        if ($month >= 1 && $month <= 3) return 'winter';
        if ($month >= 4 && $month <= 6) return 'spring';
        if ($month >= 7 && $month <= 9) return 'summer';
        return 'fall';
    }
}
