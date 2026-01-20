@extends('layouts.app')

@section('title', 'Calendário de Lançamentos')

@section('content')
<div class="calendar-container" style="padding: 2rem;">
    <!-- Header with Filters -->
    <div class="calendar-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1 class="page-title" style="margin: 0; font-size: 2rem; font-weight: 800;">Calendário Semanal</h1>
        
        <div class="rating-filters" style="display: flex; align-items: center; gap: 1rem;">
            <div class="filter-title" style="font-size: 0.75rem; color: #71717a; font-weight: 600;">OCULTAR:</div>
            <label class="checkbox-label" style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: #e4e4e7; font-size: 0.9rem;">
                <input type="checkbox" {{ $hideKids ? 'checked' : '' }} onchange="toggleCalendarFilter('hide_kids', this.checked)">
                <span>Kids</span>
            </label>
            <label class="checkbox-label" style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: #e4e4e7; font-size: 0.9rem;">
                <input type="checkbox" {{ $hideAdult ? 'checked' : '' }} onchange="toggleCalendarFilter('hide_adult', this.checked)">
                <span>+18</span>
            </label>
        </div>
    </div>

    <div class="week-tabs">
        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
            <button class="week-tab {{ $loop->first ? 'active' : '' }}" onclick="openDay(event, '{{ $day }}')">
                @php
                    $daysPT = [
                        'monday' => 'Segunda', 'tuesday' => 'Terça', 'wednesday' => 'Quarta',
                        'thursday' => 'Quinta', 'friday' => 'Sexta', 'saturday' => 'Sábado', 'sunday' => 'Domingo'
                    ];
                @endphp
                {{ $daysPT[$day] }}
            </button>
        @endforeach
    </div>

    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
        <div id="{{ $day }}" class="day-content {{ $loop->first ? 'active' : '' }}">
            @if(empty($calendar[$day]))
                <div class="empty-day" style="padding: 3rem; text-align: center; color: #555;">
                    <i class="ph ph-calendar-x" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                    Sem lançamentos registrados ou data de transmissão desconhecida.<br>
                    <small>Tente atualizar a temporada em "Gerenciar" para buscar os horários.</small>
                </div>
            @else
                <div class="calendar-grid">
                @foreach($calendar[$day] as $anime)
                    @php
                        $altTitles = is_array($anime->alternative_titles) ? $anime->alternative_titles : [];
                        $englishTitle = $altTitles['en'] ?? (isset($altTitles['synonyms'][0]) ? $altTitles['synonyms'][0] : null);
                        $displayTitle = $englishTitle ?: $anime->title;
                        $displaySubtitle = $englishTitle ? $anime->title : null; // Logic from dashboard
                    @endphp
                    <div class="calendar-card" 
                         style="cursor: pointer;"
                         onclick="openModal(this)"
                         data-id="{{ $anime->id }}"
                         data-title="{{ $displayTitle }}"
                         data-english-title="{{ $displaySubtitle }}"
                         data-image="{{ $anime->main_picture_medium }}"
                         data-synopsis="{{ $anime->synopsis }}"
                         data-score="{{ $anime->mean }}"
                         data-members="{{ $anime->num_list_users }}"
                         data-year="{{ $anime->season->year ?? $year }}"
                         data-episodes="{{ $anime->num_episodes }}"
                         data-studio="{{ is_array($anime->studios) ? collect($anime->studios)->pluck('name')->implode(', ') : 'N/A' }}"
                         data-source="{{ $anime->source }}"
                         data-genres="{{ $anime->genres_list }}"
                         data-rating="{{ $anime->rating }}"
                         data-related="{{ is_array($anime->related_animes) ? json_encode($anime->related_animes) : '[]' }}">
                         
                         <img src="{{ $anime->main_picture_medium }}" class="card-cover" loading="lazy">
                         <div class="card-info">
                             <div class="card-title">{{ $displayTitle }}</div>
                         </div>
                    </div>
                @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>

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
                <div class="related-list" id="modalRelatedList"></div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* CSS do Calendario e Modal Helper overrides */
.week-tabs { 
    display: flex; gap: 0.5rem; border-bottom: 1px solid #333; margin-bottom: 2rem; 
    overflow-x: auto; 
    padding-bottom: 1px;
}
.week-tab { 
    background: none; border: none; color: #71717a; padding: 0.8rem 1.5rem; cursor: pointer; 
    font-size: 1rem; font-weight: 600;
    position: relative;
    border-bottom: 3px solid transparent;
    white-space: nowrap;
    transition: all 0.2s;
}
.week-tab:hover { color: #fff; }
.week-tab.active { color: #fff; border-bottom-color: #E50914; }

.day-content { display: none; }
.day-content.active { display: block; animation: fadeIn 0.4s ease; }

.calendar-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
    gap: 1.5rem; 
}

.calendar-card {
    background: #18181b; border-radius: 8px; overflow: hidden; position: relative;
    transition: transform 0.2s; display: block; text-decoration: none; color: inherit;
    border: 1px solid #27272a;
}
.calendar-card:hover { transform: translateY(-4px); border-color: #E50914; }

.card-cover { width: 100%; aspect-ratio: 2/3; object-fit: cover; }
.card-info { padding: 12px; }
.card-title { 
    font-weight: 600; font-size: 0.9rem; line-height: 1.4; 
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}

/* Modal Specific Styles (reusing modal.css but ensuring gold colors) */
.anime-score, .meta-match { color: #fbbf24 !important; }
.anime-score i, .meta-match i { color: #f59e0b !important; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@push('scripts')
<script>
function openDay(evt, dayName) {
    document.querySelectorAll('.day-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.week-tab').forEach(el => el.classList.remove('active'));
    document.getElementById(dayName).classList.add('active');
    evt.currentTarget.classList.add('active');
}

function toggleCalendarFilter(param, checked) {
    const url = new URL(window.location.href);
    url.searchParams.set(param, checked ? '1' : '0');
    window.location.href = url.toString();
}

// --- MODAL LOGIC (Copied from Dashboard) ---
function formatMembers(count) {
    if (count >= 1000000) return (count / 1000000).toFixed(1) + 'M';
    if (count >= 1000) return Math.floor(count / 1000) + 'K';
    return count;
}

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
    
    // 1.5 Meta Line
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
    
    if (data.synopsis && data.synopsis.length > 250) {
        readMoreBtn.style.display = 'inline-block';
        readMoreBtn.textContent = 'Ver mais';
    } else {
        readMoreBtn.style.display = 'none';
    }

    // Grid details
    document.getElementById('modalEpisodes').textContent = data.episodes ? `${data.episodes} eps` : '?';
    document.getElementById('modalYear').textContent = data.year;
    document.getElementById('modalRatingBadge').textContent = data.rating ? data.rating.replace('_', '-').toUpperCase() : 'N/A';
    document.getElementById('modalStudio').textContent = data.studio || 'N/A';
    document.getElementById('modalSource').textContent = data.source ? data.source.replace('_', ' ') : 'N/A';
    document.getElementById('modalGenres').textContent = data.genres || 'N/A';
    
    // Related Seasons
    const relatedSection = document.getElementById('modalRelatedSection');
    const relatedList = document.getElementById('modalRelatedList');
    relatedList.innerHTML = ''; 
    
    let related = [];
    try { related = JSON.parse(data.related || '[]'); } catch(e) {}
    
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

    // Show
    const modal = document.getElementById('animeModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden'; 
}

function closeModal(event) {
    if (event) event.stopPropagation();
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

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") closeModal();
});
</script>
@endpush
@endsection
