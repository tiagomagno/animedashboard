<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Services\SeasonImportService;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    protected SeasonImportService $importService;

    public function __construct(SeasonImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display the season import page.
     */
    public function index()
    {
        $availableSeasons = $this->importService->getAvailableSeasons();
        $importedSeasons = Season::latest()->with('animes')->get();

        return view('seasons.index', compact('availableSeasons', 'importedSeasons'));
    }

    /**
     * Import a season.
     */
    public function import(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:' . (now()->year + 1),
            'season' => 'required|in:winter,spring,summer,fall',
        ]);

        try {
            $stats = $this->importService->importSeason(
                $request->year,
                $request->season
            );

            return redirect()
                ->route('seasons.index')
                ->with('success', "Season imported successfully! {$stats['imported']} new animes, {$stats['updated']} updated.");
        } catch (\Exception $e) {
            return redirect()
                ->route('seasons.index')
                ->with('error', 'Failed to import season: ' . $e->getMessage());
        }
    }

    /**
     * Import a complete year (all 4 seasons).
     */
    public function importYear(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:' . (now()->year + 1),
            'force' => 'nullable',
        ]);

        try {
            $year = $request->year;
            $force = $request->boolean('force');
            $seasons = ['winter', 'spring', 'summer', 'fall'];
            
            $totalStats = [
                'total' => 0,
                'imported' => 0,
                'updated' => 0,
                'skipped_seasons' => [],
            ];

            foreach ($seasons as $season) {
                // Verificar se a temporada já foi importada (apenas se não forçado)
                if (!$force) {
                    $existingSeason = Season::where('year', $year)
                        ->where('season', $season)
                        ->first();
                    
                    if ($existingSeason && $existingSeason->imported_at) {
                        $totalStats['skipped_seasons'][] = ucfirst($season);
                        continue; // Pular temporada já importada
                    }
                }
                
                $stats = $this->importService->importSeason($year, $season);
                
                $totalStats['total'] += $stats['total'];
                $totalStats['imported'] += $stats['imported'];
                $totalStats['updated'] += $stats['updated'];
            }

            $message = "Year {$year} processed! {$totalStats['imported']} new, {$totalStats['updated']} updated.";
            
            if (!empty($totalStats['skipped_seasons'])) {
                $skipped = implode(', ', $totalStats['skipped_seasons']);
                $message .= " Skipped (already exists): {$skipped}. Use 'Update' to force refresh.";
            }

            return redirect()
                ->route('seasons.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('seasons.index')
                ->with('error', 'Failed to import year: ' . $e->getMessage());
        }
    }

    /**
     * Update season stats.
     */
    public function updateStats(Season $season)
    {
        try {
            $stats = $this->importService->updateSeasonStats($season);

            return redirect()
                ->back()
                ->with('success', "Stats updated! {$stats['updated']} animes updated.");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update stats: ' . $e->getMessage());
        }
    }

    /**
     * Delete a season.
     */
    public function destroy(Season $season)
    {
        $season->delete();

        return redirect()
            ->route('seasons.index')
            ->with('success', 'Season deleted successfully.');
    }
}
