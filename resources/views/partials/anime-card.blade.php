<a href="{{ route('dashboard.show', $anime) }}" class="anime-card">
    @if($anime->main_picture)
        <img src="{{ $anime->main_picture }}" alt="{{ $anime->title }}" class="anime-card-image">
    @else
        <div class="anime-card-image" style="background: var(--netflix-gray); display: flex; align-items: center; justify-content: center;">
            <span style="font-size: 3rem; opacity: 0.3;">📺</span>
        </div>
    @endif
    
    <div class="anime-card-overlay">
        <h3 class="anime-card-title">{{ $anime->title }}</h3>
        
        <div class="anime-card-meta">
            @if($anime->mean)
                <span class="score score-mal">
                    MAL {{ number_format($anime->mean, 1) }}
                </span>
            @endif
            
            @if($anime->review)
                @php
                    $editorialScore = $anime->review->final_score;
                    $scoreClass = $editorialScore >= 8 ? 'score-high' : ($editorialScore >= 6 ? 'score-medium' : 'score-low');
                @endphp
                <span class="score {{ $scoreClass }}">
                    ⭐ {{ number_format($editorialScore, 1) }}
                </span>
            @endif
        </div>
    </div>
</a>
