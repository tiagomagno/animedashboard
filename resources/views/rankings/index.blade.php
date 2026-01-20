@extends('layouts.app')

@section('title', 'Rankings')

@section('content')
<div class="rankings-page">
    <!-- Page Header -->
    <div class="page-header-rankings">
        <h1 class="page-title">Rankings & Trending</h1>
        <p class="page-subtitle">Os melhores animes em destaque</p>
    </div>

    <!-- Trending & Popular Series -->
    <section class="ranking-section">
        <div class="section-header-ranking">
            <h2 class="section-title-ranking">Trending & Popular Series</h2>
            <div class="tabs-ranking">
                <button class="tab-ranking active" data-tab="trending">Trending</button>
                <button class="tab-ranking" data-tab="popular">Popular</button>
            </div>
        </div>

        <div class="ranking-carousel" id="trendingCarousel">
            @foreach($trending as $index => $anime)
                <div class="ranking-card-large">
                    <div class="ranking-number">{{ $index + 1 }}</div>
                    <div class="ranking-card-image">
                        <img src="{{ $anime->main_picture_large }}" alt="{{ $anime->title }}" loading="lazy">
                        <div class="ranking-overlay">
                            <div class="ranking-stats">
                                @if($anime->mean)
                                    <span class="stat-item"><i class="ph-fill ph-star"></i> {{ number_format($anime->mean, 2) }}</span>
                                @endif
                                @if($anime->num_list_users)
                                    <span class="stat-item"><i class="ph ph-users"></i> {{ number_format($anime->num_list_users) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="ranking-card-info">
                        <h3 class="ranking-card-title">{{ $anime->title }}</h3>
                        <p class="ranking-card-meta">{{ $anime->media_type ? strtoupper($anime->media_type) : 'TV' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Top 10 MyAnimeList -->
    <section class="ranking-section">
        <div class="section-header-ranking">
            <h2 class="section-title-ranking">Top 10 Maiores Notas MyAnimeList</h2>
            <a href="#" class="view-all-link">Ver todos <i class="ph-bold ph-arrow-right"></i></a>
        </div>

        <div class="ranking-list">
            @foreach($topMal as $index => $anime)
                <div class="ranking-list-item" onclick="openModal(this)"
                     data-id="{{ $anime->id }}"
                     data-title="{{ $anime->title }}"
                     data-english-title="{{ $anime->alternative_titles['en'] ?? '' }}"
                     data-image="{{ $anime->main_picture_large }}"
                     data-synopsis="{{ $anime->synopsis }}"
                     data-score="{{ $anime->mean }}"
                     data-members="{{ $anime->num_list_users }}"
                     data-year="{{ $anime->season->year }}"
                     data-episodes="{{ $anime->num_episodes }}"
                     data-studio="{{ is_array($anime->studios) ? collect($anime->studios)->pluck('name')->implode(', ') : 'N/A' }}"
                     data-source="{{ $anime->source }}"
                     data-genres="{{ $anime->genres_list }}"
                     data-rating="{{ $anime->rating }}">
                    
                    <div class="rank-position rank-{{ $index + 1 }}">{{ $index + 1 }}</div>
                    <div class="rank-poster">
                        <img src="{{ $anime->main_picture_medium }}" alt="{{ $anime->title }}" loading="lazy">
                    </div>
                    <div class="rank-info">
                        <h3 class="rank-title">{{ $anime->title }}</h3>
                        <p class="rank-subtitle">{{ $anime->alternative_titles['en'] ?? '' }}</p>
                        <div class="rank-meta">
                            <span class="rank-score"><i class="ph-fill ph-star"></i> {{ number_format($anime->mean, 2) }}</span>
                            <span class="rank-type">{{ strtoupper($anime->media_type ?? 'TV') }}</span>
                            <span class="rank-year">{{ $anime->season->year }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Top 10 da Temporada -->
    <section class="ranking-section">
        <div class="section-header-ranking">
            <h2 class="section-title-ranking">Top 10 {{ ucfirst($currentSeason) }} {{ $currentYear }}</h2>
            <a href="/?year={{ $currentYear }}&season={{ $currentSeason }}" class="view-all-link">Ver todos <i class="ph-bold ph-arrow-right"></i></a>
        </div>

        <div class="ranking-grid">
            @foreach($topSeason as $index => $anime)
                <div class="ranking-grid-item" onclick="openModal(this)"
                     data-id="{{ $anime->id }}"
                     data-title="{{ $anime->title }}"
                     data-english-title="{{ $anime->alternative_titles['en'] ?? '' }}"
                     data-image="{{ $anime->main_picture_large }}"
                     data-synopsis="{{ $anime->synopsis }}"
                     data-score="{{ $anime->mean }}"
                     data-members="{{ $anime->num_list_users }}"
                     data-year="{{ $anime->season->year }}"
                     data-episodes="{{ $anime->num_episodes }}"
                     data-studio="{{ is_array($anime->studios) ? collect($anime->studios)->pluck('name')->implode(', ') : 'N/A' }}"
                     data-source="{{ $anime->source }}"
                     data-genres="{{ $anime->genres_list }}"
                     data-rating="{{ $anime->rating }}">
                    
                    <div class="grid-rank-badge">{{ $index + 1 }}</div>
                    <img src="{{ $anime->main_picture_large }}" alt="{{ $anime->title }}" class="grid-poster" loading="lazy">
                    <div class="grid-info">
                        <h4 class="grid-title">{{ $anime->title }}</h4>
                        <div class="grid-score">
                            <i class="ph-fill ph-star"></i> {{ number_format($anime->mean, 2) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>

<style>
/* Rankings Page Styles */
.rankings-page {
    padding: var(--spacing-xl) 0;
    max-width: var(--max-width);
    margin: 0 auto;
}

.page-header-rankings {
    text-align: center;
    margin-bottom: var(--spacing-2xl);
}

.page-title {
    font-family: 'League Spartan', sans-serif;
    font-size: 3rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: var(--spacing-sm);
}

.page-subtitle {
    font-size: 1.1rem;
    color: var(--text-muted);
}

/* Section */
.ranking-section {
    margin-bottom: 4rem;
}

.section-header-ranking {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-xl);
}

.section-title-ranking {
    font-family: 'League Spartan', sans-serif;
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-primary);
}

.tabs-ranking {
    display: flex;
    gap: var(--spacing-sm);
}

.tab-ranking {
    padding: 8px 20px;
    background: transparent;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-full);
    color: var(--text-secondary);
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all var(--transition-base);
}

.tab-ranking.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: #000;
}

.view-all-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: gap var(--transition-base);
}

.view-all-link:hover {
    gap: 8px;
}

/* Trending Carousel */
.ranking-carousel {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: var(--spacing-lg);
}

.ranking-card-large {
    position: relative;
    cursor: pointer;
    transition: transform var(--transition-base);
}

.ranking-card-large:hover {
    transform: translateY(-8px);
}

.ranking-number {
    position: absolute;
    top: -10px;
    left: -10px;
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: #000;
    font-weight: 800;
    font-size: 1.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    box-shadow: 0 4px 12px rgba(167, 242, 5, 0.4);
}

.ranking-card-image {
    position: relative;
    aspect-ratio: 2/3;
    border-radius: var(--radius-md);
    overflow: hidden;
}

.ranking-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ranking-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    padding: var(--spacing-md);
    opacity: 0;
    transition: opacity var(--transition-base);
}

.ranking-card-large:hover .ranking-overlay {
    opacity: 1;
}

.ranking-stats {
    display: flex;
    gap: var(--spacing-md);
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 4px;
    color: #fff;
    font-size: 0.85rem;
    font-weight: 600;
}

.ranking-card-info {
    margin-top: var(--spacing-sm);
}

.ranking-card-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ranking-card-meta {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
}

/* Ranking List */
.ranking-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.ranking-list-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    background: var(--bg-tertiary);
    padding: var(--spacing-md);
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: all var(--transition-base);
}

.ranking-list-item:hover {
    background: rgba(255, 255, 255, 0.05);
    transform: translateX(8px);
}

.rank-position {
    font-family: 'League Spartan', sans-serif;
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-muted);
    min-width: 60px;
    text-align: center;
}

.rank-position.rank-1 { color: #FFD700; }
.rank-position.rank-2 { color: #C0C0C0; }
.rank-position.rank-3 { color: #CD7F32; }

.rank-poster {
    width: 80px;
    height: 120px;
    border-radius: var(--radius-sm);
    overflow: hidden;
    flex-shrink: 0;
}

.rank-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rank-info {
    flex: 1;
}

.rank-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.rank-subtitle {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-bottom: var(--spacing-sm);
}

.rank-meta {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    font-size: 0.85rem;
}

.rank-score {
    color: #fbbf24;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.rank-type, .rank-year {
    color: var(--text-muted);
}

/* Ranking Grid */
.ranking-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: var(--spacing-lg);
}

.ranking-grid-item {
    position: relative;
    cursor: pointer;
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: transform var(--transition-base);
}

.ranking-grid-item:hover {
    transform: scale(1.05);
}

.grid-rank-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    width: 36px;
    height: 36px;
    background: var(--primary-color);
    color: #000;
    font-weight: 800;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.grid-poster {
    width: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
    display: block;
}

.grid-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.95), transparent);
    padding: var(--spacing-md);
}

.grid-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.grid-score {
    color: #fbbf24;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .page-title {
        font-size: 2rem;
    }
    
    .section-header-ranking {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
    }
    
    .ranking-carousel {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    }
    
    .ranking-list-item {
        flex-direction: column;
        text-align: center;
    }
    
    .rank-position {
        min-width: auto;
    }
}
</style>
@endsection
