<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MyAnimeList API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for MyAnimeList API v2 integration
    |
    */

    'client_id' => env('MAL_CLIENT_ID'),
    'api_base_url' => env('MAL_API_BASE_URL', 'https://api.myanimelist.net/v2'),
    'cache_ttl' => env('MAL_CACHE_TTL', 3600), // 1 hour default
    
    'seasons' => [
        'winter' => 'Winter',
        'spring' => 'Spring',
        'summer' => 'Summer',
        'fall' => 'Fall',
    ],
    
    'fields' => [
        'id',
        'title',
        'alternative_titles',
        'main_picture',
        'mean',
        'num_list_users',
        'popularity',
        'rating',
        'status',
        'num_episodes',
        'genres',
        'start_season',
        'media_type',
        'synopsis',
        'studios',
        'start_date',
        'source',
        'rank',
        'broadcast',
        'related_anime'
    ],
];
