@extends('layouts.app')

@section('title', 'Dashboard - ' . $year)

@section('content')
<!-- Filter Bar -->
<!-- Novo Header de Estatísticas e Filtros -->
<style>
    /* Container Principal do Dashboard Header */
    .dashboard-header-wrapper {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        margin-bottom: 2rem;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* === BLOCO DE ESTATÍSTICAS (BOXED) === */
    .stats-boxed-container {
        display: flex;
        align-items: center;
        gap: 3rem;
        padding: 1.5rem 2rem;
        border: 1px solid #333; /* Contorno solicitado */
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.02); /* Leve fundo para destacar */
    }

    .stat-year-big {
        font-family: 'League Spartan', sans-serif;
        font-weight: 800;
        font-size: 3.5rem;
        color: var(--primary-color);
        line-height: 1;
        letter-spacing: -2px;
    }

    .stats-data-grid {
        display: flex;
        gap: 3rem; /* Mais espaçamento entre os itens */
        flex-wrap: wrap;
    }

    .stat-item-modern {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .stat-label-modern {
        font-size: 0.65rem;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
    }

    .stat-value-modern {
        font-size: 1.25rem;
        font-weight: 800;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .stat-value-modern.total-highlight { color: #ff4d4d; }
    .stat-value-modern.neon { color: var(--primary-color); }


    /* === BARRA DE NAVEGAÇÃO E FILTROS (INFERIOR) === */
    .nav-filters-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #222;
        padding-bottom: 5px; /* Ajuste fino para alinhar tabs */
        flex-wrap: wrap;
        gap: 1rem;
    }

    /* Tabs de Season (Esquerda) */
    .season-tabs-group {
        display: flex;
        gap: 2rem;
    }
    
    .season-tab-min {
        padding-bottom: 1rem;
        text-decoration: none;
        color: #666;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-bottom: 2px solid transparent;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        position: relative;
        top: 1px; /* Para a borda ativa sobrepor a borda da barra */
    }
    
    .season-tab-min:hover { color: #888; }
    
    .season-tab-min.active {
        color: #fff;
        border-bottom-color: var(--primary-color);
    }
    
    /* Badge de contagem */
    .season-count-badge {
        font-size: 0.7rem;
        background: #222;
        padding: 1px 5px;
        border-radius: 4px;
        color: #888;
    }
    .season-tab-min.active .season-count-badge {
        background: var(--primary-color);
        color: #000;
        font-weight: 600;
    }


    /* Grupo de Filtros (Direita) */
    .filters-right-group {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 0.5rem; /* Leve ajuste vertical */
    }

    /* Container Escuro dos Filtros */
    .filters-container-dark {
        display: flex;
        align-items: center;
        background: #0a0a0a;
        padding: 6px 16px;
        border-radius: 8px;
        border: 1px solid #222;
        gap: 1.5rem;
    }

    /* Filter Pills Group */
    .filter-pills {
        display: flex;
        gap: 4px;
        padding-right: 1.5rem;
        border-right: 1px solid #333;
    }

    .filter-pill {
        padding: 5px 10px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #666;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.2s;
        text-transform: uppercase;
    }
    .filter-pill:hover { color: #fff; }
    .filter-pill.active { background: #333; color: #fff; }

    /* Hide Controls */
    .hide-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.75rem;
        color: #666;
        font-weight: 600;
    }
    
    .checkbox-custom {
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        color: #fff;
    }
    .checkbox-custom input {
        accent-color: #ff4d4d; 
        width: 14px; 
        height: 14px;
        cursor: pointer;
    }
</style>

<div class="dashboard-header-wrapper">
    <!-- 1. Bloco de Estatísticas (Boxed) -->
    <div class="stats-boxed-container">
        <div class="stat-year-big">{{ $year }}</div>
        
        <div class="stats-data-grid">
            <div class="stat-item-modern">
                <span class="stat-label-modern">Total Animes</span>
                <span class="stat-value-modern total-highlight">{{ $totalYearCount }}</span>
            </div>
            <div class="stat-item-modern">
                <span class="stat-label-modern">Qtd Season</span>
                <span class="stat-value-modern neon">{{ $animes->count() }}</span>
            </div>
            <div class="stat-item-modern">
                <span class="stat-label-modern">Média Score (MAL)</span>
                <span class="stat-value-modern neon">{{ number_format($stats['avg_mal_score'] ?? 0, 2) }}</span>
            </div>
            <div class="stat-item-modern">
                <span class="stat-label-modern">Big Score (MAL)</span>
                <span class="stat-value-modern neon">{{ number_format($animes->max('mean') ?? 0, 2) }}</span>
            </div>
            <div class="stat-item-modern">
                <span class="stat-label-modern">Minor Score (MAL)</span>
                <span class="stat-value-modern neon">{{ number_format($animes->min('mean') ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Barra de Navegação + Filtros (Clean & Functional) -->
    <div class="nav-filters-bar">
        <!-- Esquerda: Tabs de Season -->
        <div class="season-tabs-group">
            <a href="?year={{ $year }}&season=all&type={{ $mediaType }}&hide_kids={{ $hideKids ? '1' : '0' }}&hide_adult={{ $hideAdult ? '1' : '0' }}" 
               class="season-tab-min {{ $seasonName === 'all' ? 'active' : '' }}">
                ALL SEASONS
            </a>
            @foreach(['winter', 'spring', 'summer', 'fall'] as $seasonKey)
                <a href="?year={{ $year }}&season={{ $seasonKey }}&type={{ $mediaType }}&hide_kids={{ $hideKids ? '1' : '0' }}&hide_adult={{ $hideAdult ? '1' : '0' }}" 
                   class="season-tab-min {{ $seasonName === $seasonKey ? 'active' : '' }}">
                    {{ strtoupper($seasonKey) }}
                    @if(isset($statsBySeason[$seasonKey]))
                        <span class="season-count-badge">{{ $statsBySeason[$seasonKey]['total'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        <!-- Direita: Filtros (Clean Design) -->
        <div class="filters-right-group" style="gap: 1.5rem; background: transparent; padding: 0; border: none;">
            <div class="filters-label" style="font-size: 0.75rem; font-weight: 800; color: #444; letter-spacing: 0.5px; margin-right: 0.5rem;">FILTROS</div>

            <!-- Type Filters Clean -->
            <div class="filter-pills-clean" style="display: flex; gap: 1.5rem;">
                @foreach(['all' => 'ALL', 'tv'=>'TV', 'ona'=>'ONA', 'ova'=>'OVA', 'movie'=>'MOVIE', 'special'=>'SPECIAL'] as $type => $label)
                    <a href="?year={{ $year }}&season={{ $seasonName }}&type={{ $type }}&hide_kids={{ $hideKids ? '1' : '0' }}&hide_adult={{ $hideAdult ? '1' : '0' }}" 
                       class="filter-tab-clean {{ $mediaType === $type ? 'active' : '' }}"
                       style="text-decoration: none; color: {{ $mediaType === $type ? '#fff' : '#666'}}; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; position: relative; transition: color 0.2s;">
                        {{ $label }}
                        @if($mediaType === $type)
                            <span style="position: absolute; bottom: -8px; left: 0; width: 100%; height: 2px; background: var(--primary-color);"></span>
                        @endif
                    </a>
                @endforeach
            </div>

            <!-- More Filters (+) Dropdown -->
            <div class="more-filters-dropdown-wrapper" style="position: relative; margin-left: 0.5rem; border-left: 1px solid #333; padding-left: 1rem;">
                <button class="btn-more-filters" onclick="toggleMoreFilters(event)" style="background: none; border: none; color: #666; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center;">
                    <i class="ph-bold ph-plus" style="font-size: 1.1rem; transition: color 0.2s;"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div class="more-filters-menu" id="moreFiltersMenu" style="display: none; position: absolute; top: calc(100% + 10px); right: 0; width: 300px; background: #111; border: 1px solid #333; border-radius: 12px; padding: 1.5rem; z-index: 1000; box-shadow: 0 10px 40px rgba(0,0,0,0.6);">
                    
                    <!-- Hide Options -->
                    <div class="filter-section" style="margin-bottom: 1.5rem;">
                        <div class="filter-section-title" style="font-size: 0.7rem; font-weight: 800; color: #555; text-transform: uppercase; margin-bottom: 0.8rem;">Ocultar Conteúdo</div>
                        <label class="checkbox-row" style="display: flex; align-items: center; gap: 10px; margin-bottom: 0.5rem; cursor: pointer; font-size: 0.85rem; color: #ccc;">
                            <input type="checkbox" {{ $hideKids ? 'checked' : '' }} onchange="toggleFilter('hide_kids', this.checked)" style="accent-color: #ff4d4d; width: 14px; height: 14px;">
                            <span>Kids</span>
                        </label>
                        <label class="checkbox-row" style="display: flex; align-items: center; gap: 10px; margin-bottom: 0.5rem; cursor: pointer; font-size: 0.85rem; color: #ccc;">
                            <input type="checkbox" {{ $hideAdult ? 'checked' : '' }} onchange="toggleFilter('hide_adult', this.checked)" style="accent-color: #ff4d4d; width: 14px; height: 14px;">
                            <span>+18 / Adult</span>
                        </label>
                    </div>

                    <div class="filter-divider" style="height: 1px; background: #222; margin: 1rem 0;"></div>

                    <!-- Genre Filter (Client Side) -->
                    <div class="filter-section">
                        <div class="filter-section-title" style="font-size: 0.7rem; font-weight: 800; color: #555; text-transform: uppercase; margin-bottom: 0.8rem;">Gêneros (Season Atual)</div>
                        
                        @php
                            $allGenres = $animes->pluck('genres')->flatten(1)->pluck('name')->unique()->sort()->values();
                        @endphp
                        
                        <div class="genre-grid" style="display: flex; flex-wrap: wrap; gap: 6px; max-height: 200px; overflow-y: auto;">
                            <!-- Botão limpar -->
                             <div class="genre-tag active" onclick="filterByGenre('all', this)" 
                                  style="font-size: 0.7rem; background: #222; padding: 4px 10px; border-radius: 4px; color: #fff; cursor: pointer;border: 1px solid var(--primary-color);">
                                  Todos
                            </div>

                            @foreach($allGenres as $genre)
                                <div class="genre-tag" onclick="filterByGenre('{{ $genre }}', this)" 
                                     style="font-size: 0.7rem; background: #1a1a1a; padding: 4px 10px; border-radius: 4px; color: #888; cursor: pointer; border: 1px solid transparent; transition: all 0.2s;">
                                    {{ $genre }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // === Logic for "More Filters" Dropdown ===
    function toggleMoreFilters(e) {
        if(e) e.stopPropagation();
        const menu = document.getElementById('moreFiltersMenu');
        const btn = document.querySelector('.btn-more-filters i');
        
        if(menu.style.display === 'block') {
            menu.style.display = 'none';
            btn.style.color = '#666';
        } else {
            menu.style.display = 'block';
            btn.style.color = 'var(--primary-color)';
        }
    }

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        const menu = document.getElementById('moreFiltersMenu');
        const btn = document.querySelector('.btn-more-filters');
        if (menu && menu.style.display === 'block') {
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.style.display = 'none';
                document.querySelector('.btn-more-filters i').style.color = '#666';
            }
        }
    });

    // === Client-Side Genre Filter ===
    function filterByGenre(genre, element) {
        // Visual Update
        document.querySelectorAll('.genre-tag').forEach(t => {
            t.style.background = '#1a1a1a';
            t.style.color = '#888';
            t.style.borderColor = 'transparent';
        });
        
        element.style.background = '#222';
        element.style.color = '#fff';
        element.style.borderColor = 'var(--primary-color)';

        // Logic
        const cards = document.querySelectorAll('.anime-card');
        let count = 0;
        
        cards.forEach(card => {
            const genresStr = card.dataset.genres || '';
            const genres = genresStr.split(',').map(s => s.trim());
            
            if (genre === 'all' || genres.includes(genre)) {
                card.style.display = 'flex';
                count++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update empty state if 0
        // (Optional: Implement Empty State Toggle)
    }

    // === Calendar Bottom Date Override (Executa ao carregar) ===
    document.addEventListener('DOMContentLoaded', () => {
        const calContainer = document.querySelector('.calendar-days');
        if(calContainer) {
            calContainer.innerHTML = '';
            calContainer.style.display = 'flex';
            calContainer.style.gap = '2rem';
            calContainer.style.height = 'auto';
            calContainer.style.minHeight = '150px';
            
            const today = new Date();
            const options = { weekday: 'long', day: 'numeric', month: 'long' };
            const todayStr = today.toLocaleDateString('pt-BR', options);

            const todayDiv = document.createElement('div');
            todayDiv.className = 'day-column';
            todayDiv.style.flex = '1';
            todayDiv.style.background = 'transparent';
            
            // Aqui precisariamos dos animes de hoje reais. 
            // Como não temos no JS, vamos por um botão CTA claro.
            
            todayDiv.innerHTML = `
                <div class="day-header" style="text-align: left; font-size: 1.1rem; margin-bottom: 1.5rem; color: #fff; text-transform: capitalize; display: flex; align-items: center; gap: 10px;">
                    <i class="ph-fill ph-calendar-check" style="color: var(--primary-color);"></i>
                    Animes de Hoje <span style="color: #666; font-size: 0.8rem; font-weight: 400; text-transform: none;">(${todayStr})</span>
                </div>
                
                <div style="display: flex; align-items: center; justify-content: space-between; background: #1a1a1a; padding: 2rem; border-radius: 12px; border: 1px dashed #333;">
                    <div style="color: #888; font-size: 0.9rem;">
                        Confira a grade completa de lançamentos e horários.
                    </div>
                    <a href="{{ route('calendar.index') }}" style="background: var(--primary-color); color: #000; padding: 10px 24px; border-radius: 8px; font-weight: 800; text-decoration: none; font-size: 0.85rem; transition: transform 0.2s; display: inline-flex; align-items: center; gap: 8px;">
                        VER GRADE DE HOJE <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            `;
            
            calContainer.appendChild(todayDiv);
        }
    });
</script>

<!-- Anime Grid -->
<!-- Anime View: Grid or List based on context -->
@if(isset($isRankingView) && $isRankingView)
    <div class="ranking-container">
        <div class="ranking-header">
            <h2>Top 100 Animes</h2>
            <p style="color: var(--text-muted);">Ordenado por {{ request('ranking') == 'popularity' ? 'Popularidade' : (request('ranking') == 'members' ? 'Membros' : 'Score') }}</p>
        </div>

        <div class="ranking-list">
            @foreach($animes as $index => $anime)
                @php
                    $altTitles = is_array($anime->alternative_titles) ? $anime->alternative_titles : [];
                    $englishTitle = $altTitles['en'] ?? (isset($altTitles['synonyms'][0]) ? $altTitles['synonyms'][0] : null);
                    $displayTitle = $englishTitle ?: $anime->title;
                @endphp
                <div class="ranking-item" onclick="openModal(this)"
                     style="cursor: pointer;"
                     data-id="{{ $anime->id }}"
                     data-title="{{ $displayTitle }}"
                     data-english-title="{{ $displayTitle !== $anime->title ? $anime->title : '' }}"
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
                    
                    <div class="ranking-number">#{{ $index + 1 }}</div>
                    
                    <div class="ranking-poster">
                        <img src="{{ $anime->main_picture_large }}" loading="lazy" alt="{{ $anime->title }}">
                    </div>
                    
                    <div class="ranking-info">
                        <h3 class="ranking-title">{{ $displayTitle }}</h3>
                        <div class="ranking-meta">
                            <span class="meta-genres">{{ Str::limit($anime->genres_list, 40) }}</span>
                            <span class="meta-dot">•</span>
                            <span>{{ $anime->media_type ? strtoupper($anime->media_type) : 'TV' }}</span>
                            @if($anime->num_episodes)
                                <span class="meta-dot">•</span>
                                <span>{{ $anime->num_episodes }} eps</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ranking-stats">
                        <div class="ranking-score" style="color: {{ $anime->mean > 7.5 ? '#4ade80' : ($anime->mean > 5 ? '#facc15' : '#f87171') }}">
                            <i class="ph-fill ph-smiley"></i>
                            <span>{{ $anime->mean ? number_format($anime->mean * 10, 0) . '%' : 'N/A' }}</span>
                        </div>
                        <div class="ranking-popularity">
                            {{ $anime->num_list_users >= 1000 ? number_format($anime->num_list_users / 1000, 0) . 'K' : $anime->num_list_users }} users
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@elseif($animes->count() > 0)
    <div class="anime-grid" id="animeGrid">
        @foreach($animes->take(20) as $anime)
            @php
                $altTitles = is_array($anime->alternative_titles) ? $anime->alternative_titles : [];
                $englishTitle = $altTitles['en'] ?? (isset($altTitles['synonyms'][0]) ? $altTitles['synonyms'][0] : null);
                
                // Title Swap Logic
                $displayTitle = $englishTitle ?: $anime->title;
                $displaySubtitle = $englishTitle ? $anime->title : null;
            @endphp
            <div class="anime-card" 
               onclick="openModal(this)"
               style="cursor: pointer;"
               data-id="{{ $anime->id }}"
               data-title="{{ $displayTitle }}"
               data-english-title="{{ $displaySubtitle }}"
               data-image="{{ $anime->main_picture_large }}"
               data-synopsis="{{ $anime->synopsis }}"
               data-score="{{ $anime->mean }}"
               data-members="{{ $anime->num_list_users }}"
               data-year="{{ $anime->season->year }}"
               data-episodes="{{ $anime->num_episodes }}"
               data-studio="{{ is_array($anime->studios) ? collect($anime->studios)->pluck('name')->implode(', ') : 'N/A' }}"
               data-source="{{ $anime->source }}"
               data-genres="{{ $anime->genres_list }}"
               data-rating="{{ $anime->rating }}"
               data-related="{{ is_array($anime->related_animes) ? json_encode($anime->related_animes) : '[]' }}">
               
                @if($anime->main_picture_large)
                    <img src="{{ $anime->main_picture_large }}" 
                         alt="{{ $anime->title }}" 
                         class="anime-card-image"
                         loading="lazy">
                @else
                    <div class="anime-card-image" style="background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                        No Image
                    </div>
                @endif
                
                <div class="anime-card-content">
                    <div class="titles-group">
                        <h3 class="anime-card-title">{{ $displayTitle }}</h3>
                        @if($displaySubtitle)
                            <div class="anime-card-subtitle">{{ $displaySubtitle }}</div>
                        @endif
                    </div>
                    
                    <div class="anime-card-meta">
                        @if($anime->mean)
                            <span class="anime-score">
                                <i class="ph-fill ph-star"></i> {{ number_format($anime->mean, 2) }}
                            </span>
                        @endif
                        
                        @if($anime->mean && $anime->num_list_users)
                            <span class="separator" style="color: var(--text-muted); opacity: 0.5;">|</span>
                        @endif
                        
                        @if($anime->num_list_users)
                            <span class="anime-members">
                                <i class="ph ph-users"></i> {{ $anime->num_list_users >= 1000000 ? number_format($anime->num_list_users / 1000000, 1) . 'M' : ($anime->num_list_users >= 1000 ? number_format($anime->num_list_users / 1000, 0) . 'K' : $anime->num_list_users) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Loading Indicator -->
    <div class="loading-indicator" id="loadingIndicator" style="display: none;">
        <div class="spinner"></div>
        <p style="margin-top: var(--spacing-md);">Carregando mais animes...</p>
    </div>
    
    <!-- Hidden animes data for JS (Infinite Scroll) -->
    <div id="hiddenAnimes" style="display: none;">
        @foreach($animes->skip(20) as $anime)
            @php
                $altTitles = is_array($anime->alternative_titles) ? $anime->alternative_titles : [];
                $englishTitle = $altTitles['en'] ?? (isset($altTitles['synonyms'][0]) ? $altTitles['synonyms'][0] : null);
                
                $displayTitle = $englishTitle ?: $anime->title;
                $displaySubtitle = $englishTitle ? $anime->title : null;
            @endphp
            <div class="anime-data" 
                 data-id="{{ $anime->id }}"
                 data-title="{{ $displayTitle }}"
                 data-english-title="{{ $displaySubtitle }}"
                 data-image="{{ $anime->main_picture_large }}"
                 data-synopsis="{{ $anime->synopsis }}"
                 data-score="{{ $anime->mean }}"
                 data-members="{{ $anime->num_list_users }}"
                 data-year="{{ $anime->season->year }}"
                 data-episodes="{{ $anime->num_episodes }}"
                 data-studio="{{ is_array($anime->studios) ? collect($anime->studios)->pluck('name')->implode(', ') : 'N/A' }}"
                 data-source="{{ $anime->source }}"
                 data-genres="{{ $anime->genres_list }}"
                 data-rating="{{ $anime->rating }}"
                 data-related="{{ is_array($anime->related_animes) ? json_encode($anime->related_animes) : '[]' }}">
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-icon"><i class="ph ph-television-simple"></i></div>
        <h2 class="empty-state-title">Nenhum anime encontrado</h2>
        <p>Tente ajustar os filtros ou importar mais temporadas.</p>
    </div>
@endif

<!-- ANIME DETAILS MODAL (Split Layout) -->
<div class="modal-overlay" id="animeModal" onclick="closeModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        
        <!-- Left Column: Poster -->
        <div class="modal-poster-col">
            <img id="modalPoster" src="" alt="Poster" class="modal-poster-img">
        </div>

        <!-- Right Column: Info & Details -->
        <div class="modal-info-col">
            <button class="modal-close" onclick="closeModal(event)">
                <i class="ph ph-x"></i>
            </button>

            <div class="modal-header-group">
                <h2 class="modal-title" id="modalTitle">Anime Title</h2>
                <h3 class="modal-subtitle" id="modalSubtitle">English Title</h3>
            </div>

            <div class="modal-meta-line">
                <span class="anime-score" id="modalScore" title="Score MAL" style="display: flex; align-items: center; gap: 0.3rem; font-size: 1.1rem;">
                    <i class="ph-fill ph-star"></i> <span>0.00</span>
                </span>
                <span id="modalMembers" title="Membros" style="color: #a1a1aa; display: flex; align-items: center; gap: 0.5rem; font-size: 1rem;">
                    <i class="ph ph-users"></i> <span>0</span>
                </span>
                <span class="meta-rating-badge" id="modalType">TV</span>
                <span class="meta-rating-badge" id="modalRatingBadge">PG-13</span>
            </div>

            <div class="synopsis-container">
                <div class="synopsis-text" id="modalSynopsis">
                    Synopsis text...
                </div>
                <button class="btn-read-more" id="readMoreBtn" onclick="toggleSynopsis()">Ver mais</button>
            </div>

            <div class="modal-info-grid">
                <div class="info-item">
                    <span class="info-label">Episódios</span>
                    <span class="info-value" id="modalEpisodes">12 eps</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Onde Assistir</span>
                    <span class="info-value" id="modalStreaming">
                        <i class="ph ph-monitor-play" style="vertical-align: middle;"></i> TV/Web
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Ano</span>
                    <span class="info-value" id="modalYear">2026</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estúdio</span>
                    <span class="info-value" id="modalStudio">Studio</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fonte</span>
                    <span class="info-value" id="modalSource">Manga</span>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Gêneros</span>
                    <span class="info-value" id="modalGenres">Action</span>
                </div>
            </div>

            <div class="related-seasons" id="modalRelatedSection" style="display: none;">
                <div class="related-title">Temporadas Relacionadas</div>
                <div class="related-list" id="modalRelatedList">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Temporary place for this style if app.css failed to update */
.anime-card {
    display: flex;
    flex-direction: column;
    height: 100%; 
}

.anime-card-content {
    display: flex;
    flex-direction: column;
    flex: 1;
    padding: var(--spacing-sm);
}

/* NOVO: Wrapper para colar os titulos */
.titles-group {
    display: flex;
    flex-direction: column;
    gap: 2px; /* Espaço ínfimo fixo entre Título e Legenda */
    margin-bottom: var(--spacing-sm);
}

.anime-card-title {
    margin: 0 !important; /* Removemos todas as margens do H3 */
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.anime-card-subtitle {
    font-size: 0.75rem;
    color: #a1a1aa; /* Zinc 400 - Melhor contraste */
    white-space: nowrap; 
    overflow: hidden;
    text-overflow: ellipsis;
    margin: 0 !important;
    line-height: 1.2;
    font-weight: 400;
    display: block;
    min-height: 0;
}

.anime-card-meta {
    margin-top: auto !important; /* Empurra pro fundo */
    padding-top: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    font-size: 0.75rem;
    color: var(--text-muted);
}

/* Golden Score & Contrast */
.anime-score {
    color: #fbbf24 !important; /* Amber 400 (Dourado Claro) */
    font-weight: 600;
}
.anime-score i {
    color: #f59e0b !important; /* Amber 500 (Dourado Rico) */
}

/* =========================================
   RANKING LIST STYLES (Anilist Inspired)
   ========================================= */
.ranking-container {
    max-width: 1000px;
    margin: 0 auto;
    padding-bottom: 4rem;
}

.ranking-header {
    margin-bottom: 2rem;
}

.ranking-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.ranking-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.ranking-item {
    display: flex;
    align-items: center;
    background: var(--bg-secondary);
    border-radius: 4px; /* Anilist uses slightly rounded corners or sharp */
    padding: 0.75rem 1rem;
    transition: transform 0.2s, background-color 0.2s;
    text-decoration: none;
    gap: 1.5rem;
}

.ranking-item:hover {
    background: var(--bg-tertiary);
    transform: scale(1.01);
}

.ranking-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-muted);
    width: 40px;
    text-align: center;
}

/* Highlight top 3 */
.ranking-item:nth-child(1) .ranking-number { color: #eab308; } /* #1 Gold */
.ranking-item:nth-child(2) .ranking-number { color: #9ca3af; } /* #2 Silver */
.ranking-item:nth-child(3) .ranking-number { color: #b45309; } /* #3 Bronze */

.ranking-poster {
    width: 48px;
    height: 70px;
    flex-shrink: 0;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}

.ranking-poster img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ranking-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.25rem;
}

.ranking-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 !important;
    line-height: 1.2;
}

.ranking-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.meta-dot {
    font-size: 0.5rem;
    opacity: 0.6;
}

.ranking-stats {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
    min-width: 100px;
}

.ranking-score {
    font-weight: 700;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.ranking-popularity {
    font-size: 0.75rem;
    color: var(--text-muted);
}

@media (max-width: 768px) {
    .ranking-meta .meta-genres {
        display: none;
    }
    .ranking-number {
        font-size: 1.2rem;
        width: 30px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Toggle filter function
function toggleFilter(param, checked) {
    const url = new URL(window.location.href);
    url.searchParams.set(param, checked ? '1' : '0');
    window.location.href = url.toString();
}

// Format members count
function formatMembers(count) {
    if (count >= 1000000) {
        return (count / 1000000).toFixed(1) + 'M';
    } else if (count >= 1000) {
        return Math.floor(count / 1000) + 'K';
    }
    return count;
}

// Modal Logic
function openModal(element) {
    const data = element.dataset;
    
    // 1. Title & Poster
    document.getElementById('modalTitle').textContent = data.title;
    
    const posterImg = document.getElementById('modalPoster');
    posterImg.src = data.image || ''; 
    posterImg.classList.remove('loaded');
    posterImg.onload = () => posterImg.classList.add('loaded');
    
    // Subtitle
    const sub = document.getElementById('modalSubtitle');
    if (data.englishTitle && data.englishTitle !== data.title) {
        sub.textContent = data.englishTitle;
        sub.style.display = 'block';
    } else {
        sub.style.display = 'none';
    }
    
    // 1.5 Meta Line (Score, Members, Type)
    const scoreVal = parseFloat(data.score) || 0;
    document.querySelector('#modalScore span').textContent = scoreVal.toFixed(2);
    document.getElementById('modalScore').style.display = scoreVal ? 'flex' : 'none';
    
    const membersVal = parseInt(data.members) || 0;
    document.querySelector('#modalMembers span').textContent = formatMembers(membersVal);
    
    document.getElementById('modalType').textContent = (data.type || 'TV').toUpperCase().replace('_', ' ');

    // 2. Synopsis
    const synopsisEl = document.getElementById('modalSynopsis');
    const readMoreBtn = document.getElementById('readMoreBtn');
    
    synopsisEl.textContent = data.synopsis || 'Sinopse não disponível.';
    synopsisEl.classList.remove('expanded');
    
    // Simple check for text length
    if (data.synopsis && data.synopsis.length > 250) {
        readMoreBtn.style.display = 'inline-block';
        readMoreBtn.textContent = 'Ver mais';
    } else {
        readMoreBtn.style.display = 'none';
    }

    // 3-9 Populating Grid
    document.getElementById('modalEpisodes').textContent = data.episodes ? `${data.episodes} eps` : '?';
    document.getElementById('modalYear').textContent = data.year;
    document.getElementById('modalRatingBadge').textContent = data.rating ? data.rating.replace('_', '-').toUpperCase() : 'N/A';
    document.getElementById('modalStudio').textContent = data.studio || 'N/A';
    document.getElementById('modalSource').textContent = data.source ? data.source.replace('_', ' ') : 'N/A';
    document.getElementById('modalGenres').textContent = data.genres || 'N/A';
    
    // 10. Related Seasons
    const relatedSection = document.getElementById('modalRelatedSection');
    const relatedList = document.getElementById('modalRelatedList');
    relatedList.innerHTML = ''; // Clear
    
    let related = [];
    try {
        related = JSON.parse(data.related || '[]');
    } catch(e) {}
    
    if (related && related.length > 0) {
        let hasRelations = false;
        related.forEach(rel => {
            const type = rel.relation_type_formatted || rel.relation_type || '';
            const title = rel.node.title;
            const badge = document.createElement('span');
            badge.className = 'related-badge';
            badge.textContent = `${title} (${type})`;
            relatedList.appendChild(badge);
            hasRelations = true;
        });
        relatedSection.style.display = hasRelations ? 'block' : 'none';
    } else {
        relatedSection.style.display = 'none';
    }

    // Show Modal
    const modal = document.getElementById('animeModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; 
}

function closeModal(event) {
    document.getElementById('animeModal').classList.remove('active');
    document.body.style.overflow = ''; 
}

function toggleSynopsis() {
    const el = document.getElementById('modalSynopsis');
    const btn = document.getElementById('readMoreBtn');
    
    if (el.classList.contains('expanded')) {
        el.classList.remove('expanded');
        btn.textContent = 'Ver mais';
    } else {
        el.classList.add('expanded');
        btn.textContent = 'Ver menos';
    }
}

// Close on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeModal();
    }
});

// Infinite Scroll Logic
let currentPage = 1;
const itemsPerPage = 20;
let isLoading = false;
let allAnimes = [];
let loadedCount = 0;

// Populate allAnimes from hidden data
document.querySelectorAll('.anime-data').forEach(item => {
    allAnimes.push({
        id: item.dataset.id,
        title: item.dataset.title,
        englishTitle: item.dataset.englishTitle,
        image: item.dataset.image,
        synopsis: item.dataset.synopsis,
        score: item.dataset.score,
        members: item.dataset.members,
        year: item.dataset.year,
        episodes: item.dataset.episodes,
        studio: item.dataset.studio,
        source: item.dataset.source,
        genres: item.dataset.genres,
        rating: item.dataset.rating,
        related: item.dataset.related
    });
});

function loadMoreAnimes() {
    if (isLoading || loadedCount >= allAnimes.length) return;
    
    isLoading = true;
    document.getElementById('loadingIndicator').style.display = 'block';
    
    setTimeout(() => {
        const grid = document.getElementById('animeGrid');
        const nextBatch = allAnimes.slice(loadedCount, loadedCount + itemsPerPage);
        
        nextBatch.forEach(anime => {
            const card = createAnimeCard(anime);
            grid.appendChild(card);
        });
        
        loadedCount += itemsPerPage;
        isLoading = false;
        document.getElementById('loadingIndicator').style.display = 'none';
    }, 500);
}

function createAnimeCard(anime) {
    const card = document.createElement('div');
    card.className = 'anime-card';
    card.style.cursor = 'pointer';
    card.onclick = function() { openModal(this); };
    
    // Set Dataset
    card.dataset.title = anime.title;
    card.dataset.englishTitle = anime.englishTitle;
    card.dataset.image = anime.image;
    card.dataset.synopsis = anime.synopsis;
    card.dataset.score = anime.score;
    card.dataset.members = anime.members;
    card.dataset.year = anime.year;
    card.dataset.episodes = anime.episodes;
    card.dataset.studio = anime.studio;
    card.dataset.source = anime.source;
    card.dataset.genres = anime.genres;
    card.dataset.rating = anime.rating;
    card.dataset.related = anime.related;
    
    const imageHtml = anime.image 
        ? `<img src="${anime.image}" alt="${anime.title}" class="anime-card-image" loading="lazy">`
        : `<div class="anime-card-image" style="background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">No Image</div>`;
    
    const scoreHtml = anime.score 
        ? `<span class="anime-score"><i class="ph-fill ph-star"></i> ${parseFloat(anime.score).toFixed(2)}</span>`
        : '';
        
    const membersHtml = anime.members 
        ? `<span class="anime-members"><i class="ph ph-users"></i> ${formatMembers(parseInt(anime.members))}</span>`
        : '';

    // Subtitle logic
    let subtitleHtml = '';
    if (anime.englishTitle && anime.englishTitle !== anime.title) {
        subtitleHtml = `<div class="anime-card-subtitle">${anime.englishTitle}</div>`;
    }
    
    card.innerHTML = `
        ${imageHtml}
        <div class="anime-card-content">
            <h3 class="anime-card-title">${anime.title}</h3>
            ${subtitleHtml}
            <div class="anime-card-meta">
                ${scoreHtml}
                ${(scoreHtml && membersHtml) ? '<span class="separator" style="color: var(--text-muted); opacity: 0.5;">|</span>' : ''}
                ${membersHtml}
            </div>
        </div>
    `;
    
    return card;
}

let scrollTimeout;
window.addEventListener('scroll', () => {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        const scrollPosition = window.innerHeight + window.scrollY;
        const threshold = document.documentElement.scrollHeight - 1000;
        
        if (scrollPosition >= threshold && !isLoading) {
            loadMoreAnimes();
        }
    }, 100);
});
</script>
@endpush
@endsection
