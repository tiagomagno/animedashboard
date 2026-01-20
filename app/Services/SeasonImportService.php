<?php

namespace App\Services;

use App\Models\Season;
use App\Models\Anime;
use App\Models\AnimeStat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SeasonImportService
{
    protected MyAnimeListService $malService;

    public function __construct(MyAnimeListService $malService)
    {
        $this->malService = $malService;
    }

    /**
     * Import a complete season from MAL.
     */
    public function importSeason(int $year, string $season): array
    {
        $stats = [
            'total' => 0,
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        try {
            DB::beginTransaction();

            // Create or get season
            $seasonModel = Season::firstOrCreate(
                ['year' => $year, 'season' => $season],
                [
                    'name' => ucfirst($season) . ' ' . $year,
                    'is_active' => true,
                ]
            );

            // Get all animes from MAL
            Log::info('Starting season import', ['year' => $year, 'season' => $season]);
            $animes = $this->malService->getAllSeasonAnimes($year, $season);
            $stats['total'] = count($animes);

            Log::info('Fetched animes from MAL', ['count' => $stats['total']]);

            // Excluded media types (not relevant for editorial analysis)
            $excludedTypes = ['music', 'pv', 'cm'];

            // Import each anime
            foreach ($animes as $animeData) {
                try {
                    $node = $animeData['node'];
                    
                    // Skip excluded media types
                    $mediaType = $node['media_type'] ?? null;
                    if (in_array($mediaType, $excludedTypes)) {
                        $stats['skipped']++;
                        continue;
                    }
                    
                    $result = $this->importAnime($seasonModel, $node);

                    if ($result === 'imported') {
                        $stats['imported']++;
                    } elseif ($result === 'updated') {
                        $stats['updated']++;
                    } else {
                        $stats['skipped']++;
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    Log::error('Error importing anime', [
                        'mal_id' => $node['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Mark season as imported
            $seasonModel->update(['imported_at' => now()]);

            DB::commit();

            Log::info('Season import completed', $stats);

            return $stats;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Season import failed', [
                'year' => $year,
                'season' => $season,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Import or update a single anime.
     */
    protected function importAnime(Season $season, array $data): string
    {
        $malId = $data['id'];

        // Check if anime already exists
        $anime = Anime::where('mal_id', $malId)->first();

        $animeData = [
            'season_id' => $season->id,
            'title' => $data['title'],
            'alternative_titles' => $data['alternative_titles'] ?? null,
            'synopsis' => $data['synopsis'] ?? null,
            'main_picture_medium' => $data['main_picture']['medium'] ?? null,
            'main_picture_large' => $data['main_picture']['large'] ?? null,
            'mean' => $data['mean'] ?? null,
            'num_list_users' => $data['num_list_users'] ?? 0,
            'popularity' => $data['popularity'] ?? null,
            'rank' => $data['rank'] ?? null,
            'rating' => $data['rating'] ?? null,
            'status' => $data['status'] ?? null,
            'media_type' => $data['media_type'] ?? null,
            'num_episodes' => $data['num_episodes'] ?? null,
            'genres' => $data['genres'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'studios' => $data['studios'] ?? null,
            'source' => $data['source'] ?? null,
            'broadcast' => $data['broadcast'] ?? null,
            'related_animes' => $data['related_anime'] ?? null,
            'last_updated_at' => now(),
        ];

        if ($anime) {
            // Update existing anime
            $anime->update($animeData);
            
            // Record stats
            $this->recordStats($anime);
            
            return 'updated';
        } else {
            // Create new anime
            $anime = Anime::create(array_merge(['mal_id' => $malId], $animeData));
            
            // Record initial stats
            $this->recordStats($anime);
            
            return 'imported';
        }
    }

    /**
     * Record current stats for an anime.
     */
    protected function recordStats(Anime $anime): void
    {
        AnimeStat::create([
            'anime_id' => $anime->id,
            'mean' => $anime->mean,
            'num_list_users' => $anime->num_list_users,
            'popularity' => $anime->popularity,
            'rank' => $anime->rank,
            'recorded_at' => now(),
        ]);
    }

    /**
     * Update stats for all animes in a season.
     */
    public function updateSeasonStats(Season $season): array
    {
        $stats = [
            'total' => 0,
            'updated' => 0,
            'errors' => 0,
        ];

        $animes = $season->animes;
        $stats['total'] = $animes->count();

        foreach ($animes as $anime) {
            try {
                $this->updateAnimeStats($anime);
                $stats['updated']++;
            } catch (Exception $e) {
                $stats['errors']++;
                Log::error('Error updating anime stats', [
                    'anime_id' => $anime->id,
                    'mal_id' => $anime->mal_id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Be nice to the API
            usleep(250000); // 250ms delay
        }

        return $stats;
    }

    /**
     * Update stats for a single anime.
     */
    public function updateAnimeStats(Anime $anime): void
    {
        $data = $this->malService->getAnime($anime->mal_id);

        if (!$data) {
            throw new Exception('Failed to fetch anime data from MAL');
        }

        // Update anime data
        $anime->update([
            'mean' => $data['mean'] ?? null,
            'num_list_users' => $data['num_list_users'] ?? 0,
            'popularity' => $data['popularity'] ?? null,
            'rank' => $data['rank'] ?? null,
            'status' => $data['status'] ?? null,
            'last_updated_at' => now(),
        ]);

        // Record new stats
        $this->recordStats($anime);
    }

    /**
     * Get available seasons from MAL.
     */
    public function getAvailableSeasons(): array
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Determine current season
        $currentSeason = match (true) {
            $currentMonth >= 1 && $currentMonth <= 3 => 'winter',
            $currentMonth >= 4 && $currentMonth <= 6 => 'spring',
            $currentMonth >= 7 && $currentMonth <= 9 => 'summer',
            default => 'fall',
        };

        $seasons = [];

        // Generate last 2 years of seasons
        for ($year = $currentYear; $year >= $currentYear - 2; $year--) {
            foreach (['fall', 'summer', 'spring', 'winter'] as $season) {
                // Don't include future seasons
                if ($year === $currentYear) {
                    $seasonOrder = ['winter' => 1, 'spring' => 2, 'summer' => 3, 'fall' => 4];
                    if ($seasonOrder[$season] > $seasonOrder[$currentSeason]) {
                        continue;
                    }
                }

                $seasons[] = [
                    'year' => $year,
                    'season' => $season,
                    'display_name' => ucfirst($season) . ' ' . $year,
                    'imported' => Season::where('year', $year)
                        ->where('season', $season)
                        ->exists(),
                ];
            }
        }

        return $seasons;
    }
}
