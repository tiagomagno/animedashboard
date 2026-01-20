@extends('layouts.app')

@section('title', $anime->title)

@section('content')
<div class="container">
    <!-- Anime Header -->
    <div style="display: grid; grid-template-columns: 300px 1fr; gap: var(--spacing-xl); margin-bottom: var(--spacing-xl);">
        <div>
            @if($anime->main_picture)
                <img src="{{ $anime->main_picture }}" alt="{{ $anime->title }}" 
                     style="width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);">
            @endif
        </div>
        
        <div>
            <h1 class="hero-title" style="margin-bottom: var(--spacing-sm);">{{ $anime->title }}</h1>
            
            <div style="display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                @if($anime->mean)
                    <div>
                        <div class="stat-label">Score MAL</div>
                        <div class="stat-value" style="font-size: 2rem;">{{ number_format($anime->mean, 2) }}</div>
                    </div>
                @endif
                
                @if($anime->review)
                    <div>
                        <div class="stat-label">Score Editorial</div>
                        <div class="stat-value" style="font-size: 2rem;">{{ number_format($anime->review->final_score, 2) }}</div>
                    </div>
                @endif
                
                @if($anime->num_list_users)
                    <div>
                        <div class="stat-label">Membros</div>
                        <div class="stat-value" style="font-size: 2rem;">{{ number_format($anime->num_list_users) }}</div>
                    </div>
                @endif
            </div>
            
            @if($anime->synopsis)
                <p class="text-muted" style="margin-bottom: var(--spacing-md);">{{ $anime->synopsis }}</p>
            @endif
            
            <div style="display: flex; gap: var(--spacing-sm); flex-wrap: wrap; margin-bottom: var(--spacing-md);">
                @if($anime->status)
                    <span class="score score-mal">{{ ucfirst(str_replace('_', ' ', $anime->status)) }}</span>
                @endif
                
                @if($anime->media_type)
                    <span class="score score-mal">{{ strtoupper($anime->media_type) }}</span>
                @endif
                
                @if($anime->num_episodes)
                    <span class="score score-mal">{{ $anime->num_episodes }} episódios</span>
                @endif
            </div>
            
            @if($anime->genres && count($anime->genres) > 0)
                <div style="margin-bottom: var(--spacing-md);">
                    <div class="stat-label">Gêneros</div>
                    <div style="display: flex; gap: var(--spacing-xs); flex-wrap: wrap;">
                        @foreach($anime->genres as $genre)
                            <span class="score score-editorial">{{ $genre['name'] }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div style="display: flex; gap: var(--spacing-sm);">
                <a href="{{ route('reviews.create', $anime) }}" class="btn btn-primary">
                    {{ $anime->review ? 'Editar Avaliação' : 'Avaliar Anime' }}
                </a>
                
                <a href="{{ route('dashboard.index', ['season_id' => $anime->season_id]) }}" class="btn btn-secondary">
                    Voltar para Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Chart -->
    @if($anime->stats->count() > 1)
    <div class="section">
        <h2 class="section-title">📊 Evolução das Métricas</h2>
        
        <div class="stat-card" style="padding: var(--spacing-lg);">
            <canvas id="statsChart" style="max-height: 400px;"></canvas>
        </div>
    </div>
    @endif

    <!-- Review Details -->
    @if($anime->review)
    <div class="section">
        <h2 class="section-title">⭐ Avaliação Editorial</h2>
        
        <div class="stat-card" style="padding: var(--spacing-lg);">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                @if($anime->review->score_story)
                <div>
                    <div class="stat-label">Roteiro</div>
                    <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($anime->review->score_story, 1) }}</div>
                </div>
                @endif
                
                @if($anime->review->score_direction)
                <div>
                    <div class="stat-label">Direção</div>
                    <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($anime->review->score_direction, 1) }}</div>
                </div>
                @endif
                
                @if($anime->review->score_animation)
                <div>
                    <div class="stat-label">Animação</div>
                    <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($anime->review->score_animation, 1) }}</div>
                </div>
                @endif
                
                @if($anime->review->score_soundtrack)
                <div>
                    <div class="stat-label">Trilha Sonora</div>
                    <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($anime->review->score_soundtrack, 1) }}</div>
                </div>
                @endif
                
                @if($anime->review->score_impact)
                <div>
                    <div class="stat-label">Impacto</div>
                    <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($anime->review->score_impact, 1) }}</div>
                </div>
                @endif
            </div>
            
            @if($anime->review->notes)
                <div>
                    <div class="stat-label">Notas</div>
                    <p class="text-muted">{{ $anime->review->notes }}</p>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>

@if($anime->stats->count() > 1)
@push('scripts')
<script>
    const ctx = document.getElementById('statsChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                {
                    label: 'Score MAL',
                    data: @json($chartData['score']),
                    borderColor: '#5B8FF9',
                    backgroundColor: 'rgba(91, 143, 249, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'Membros',
                    data: @json($chartData['members']),
                    borderColor: '#E50914',
                    backgroundColor: 'rgba(229, 9, 20, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff'
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#808080' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: { color: '#5B8FF9' },
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    title: {
                        display: true,
                        text: 'Score',
                        color: '#5B8FF9'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    ticks: { color: '#E50914' },
                    grid: { drawOnChartArea: false },
                    title: {
                        display: true,
                        text: 'Membros',
                        color: '#E50914'
                    }
                }
            }
        }
    });
</script>
@endpush
@endif
@endsection
