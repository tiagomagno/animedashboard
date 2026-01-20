<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class MyAnimeListService
{
    protected string $clientId;
    protected string $baseUrl;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->clientId = config('mal.client_id');
        $this->baseUrl = config('mal.api_base_url');
        $this->cacheTtl = config('mal.cache_ttl');

        if (!$this->clientId) {
            throw new Exception('MAL Client ID not configured');
        }
    }

    /**
     * Get anime by ID.
     */
    public function getAnime(int $malId, array $fields = null): ?array
    {
        $fields = $fields ?? config('mal.fields');
        $cacheKey = "mal_anime_{$malId}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($malId, $fields) {
            try {
                $response = Http::withHeaders([
                    'X-MAL-CLIENT-ID' => $this->clientId,
                ])->get("{$this->baseUrl}/anime/{$malId}", [
                    'fields' => implode(',', $fields),
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('MAL API Error', [
                    'mal_id' => $malId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (Exception $e) {
                Log::error('MAL API Exception', [
                    'mal_id' => $malId,
                    'error' => $e->getMessage(),
                ]);

                return null;
            }
        });
    }

    /**
     * Get animes by season.
     */
    public function getSeasonAnimes(int $year, string $season, int $limit = 100, int $offset = 0): ?array
    {
        $fields = config('mal.fields');
        $cacheKey = "mal_season_{$year}_{$season}_{$limit}_{$offset}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($year, $season, $limit, $offset, $fields) {
            try {
                $response = Http::timeout(30)
                    ->withOptions([
                        'verify' => false, // Desabilitar verificação SSL temporariamente
                    ])
                    ->withHeaders([
                        'X-MAL-CLIENT-ID' => $this->clientId,
                    ])
                    ->get("{$this->baseUrl}/anime/season/{$year}/{$season}", [
                        'fields' => implode(',', $fields),
                        'limit' => $limit,
                        'offset' => $offset,
                    ]);

                Log::info('MAL API Response - Season', [
                    'year' => $year,
                    'season' => $season,
                    'status' => $response->status(),
                    'has_data' => isset($response->json()['data']),
                    'count' => isset($response->json()['data']) ? count($response->json()['data']) : 0,
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('MAL API Error - Season', [
                    'year' => $year,
                    'season' => $season,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (Exception $e) {
                Log::error('MAL API Exception - Season', [
                    'year' => $year,
                    'season' => $season,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return null;
            }
        });
    }

    /**
     * Get all animes from a season (handles pagination).
     */
    public function getAllSeasonAnimes(int $year, string $season): array
    {
        $allAnimes = [];
        $offset = 0;
        $limit = 100;
        $hasMore = true;

        while ($hasMore) {
            $data = $this->getSeasonAnimes($year, $season, $limit, $offset);

            if (!$data || !isset($data['data'])) {
                break;
            }

            $animes = $data['data'];
            $allAnimes = array_merge($allAnimes, $animes);

            // Check if there are more pages
            $hasMore = isset($data['paging']['next']);
            $offset += $limit;

            // Safety limit to avoid infinite loops
            if ($offset > 1000) {
                Log::warning('Season import reached safety limit', [
                    'year' => $year,
                    'season' => $season,
                    'offset' => $offset,
                ]);
                break;
            }

            // Be nice to the API
            if ($hasMore) {
                usleep(250000); // 250ms delay between requests
            }
        }

        return $allAnimes;
    }

    /**
     * Search anime by query.
     */
    public function searchAnime(string $query, int $limit = 10): ?array
    {
        $fields = config('mal.fields');
        $cacheKey = "mal_search_" . md5($query) . "_{$limit}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($query, $limit, $fields) {
            try {
                $response = Http::withHeaders([
                    'X-MAL-CLIENT-ID' => $this->clientId,
                ])->get("{$this->baseUrl}/anime", [
                    'q' => $query,
                    'limit' => $limit,
                    'fields' => implode(',', $fields),
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                Log::error('MAL API Error - Search', [
                    'query' => $query,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (Exception $e) {
                Log::error('MAL API Exception - Search', [
                    'query' => $query,
                    'error' => $e->getMessage(),
                ]);

                return null;
            }
        });
    }

    /**
     * Clear cache for a specific anime.
     */
    public function clearAnimeCache(int $malId): void
    {
        Cache::forget("mal_anime_{$malId}");
    }

    /**
     * Clear cache for a season.
     */
    public function clearSeasonCache(int $year, string $season): void
    {
        // This is a simplified version - in production, you might want to track all cache keys
        for ($offset = 0; $offset <= 1000; $offset += 100) {
            Cache::forget("mal_season_{$year}_{$season}_100_{$offset}");
        }
    }

    /**
     * Validate API connection.
     */
    public function validateConnection(): bool
    {
        try {
            $response = Http::withHeaders([
                'X-MAL-CLIENT-ID' => $this->clientId,
            ])->get("{$this->baseUrl}/anime/1", [
                'fields' => 'id,title',
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('MAL API Connection Validation Failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
