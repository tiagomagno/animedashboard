@props(['anime'])

<div class="anime-card" 
     onclick="openModal(this)"
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
            <h3 class="anime-card-title">{{ $anime->title }}</h3>
            @if(!empty($anime->alternative_titles['en']))
                <div class="anime-card-subtitle">{{ $anime->alternative_titles['en'] }}</div>
            @endif
        </div>
        
        <div class="anime-card-meta">
            @if($anime->mean)
                <span class="anime-score">
                    <i class="ph-fill ph-star"></i> {{ number_format($anime->mean, 2) }}
                </span>
            @endif
            
            @if($anime->mean && $anime->num_list_users)
                <span class="separator">|</span>
            @endif
            
            @if($anime->num_list_users)
                <span class="anime-members">
                    <i class="ph ph-users"></i> 
                    {{ $anime->num_list_users >= 1000000 ? number_format($anime->num_list_users / 1000000, 1) . 'M' : ($anime->num_list_users >= 1000 ? number_format($anime->num_list_users / 1000, 0) . 'K' : $anime->num_list_users) }}
                </span>
            @endif
        </div>
    </div>
</div>