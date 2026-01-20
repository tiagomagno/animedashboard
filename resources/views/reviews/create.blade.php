@extends('layouts.app')

@section('title', 'Avaliar ' . $anime->title)

@section('content')
<div class="container">
    <div class="hero">
        <div class="hero-content">
            <h1 class="hero-title">{{ $review ? 'Editar' : 'Criar' }} Avaliação</h1>
            <p class="hero-subtitle">{{ $anime->title }}</p>
        </div>
    </div>

    <div style="max-width: 800px; margin: 0 auto;">
        <form action="{{ route('reviews.store', $anime) }}" method="POST">
            @csrf
            
            <div class="stat-card" style="padding: var(--spacing-lg); margin-bottom: var(--spacing-md);">
                <h3 style="margin-bottom: var(--spacing-md);">Critérios de Avaliação</h3>
                <p class="text-muted" style="margin-bottom: var(--spacing-md);">
                    Avalie cada critério de 0 a 10. A nota final será calculada automaticamente.
                </p>
                
                <div style="display: grid; gap: var(--spacing-md);">
                    <!-- Roteiro -->
                    <div class="form-group">
                        <label class="form-label" for="score_story">
                            📖 Roteiro
                        </label>
                        <input 
                            type="number" 
                            name="score_story" 
                            id="score_story" 
                            class="form-input"
                            min="0" 
                            max="10" 
                            step="0.1"
                            value="{{ old('score_story', $review->score_story ?? '') }}"
                        >
                    </div>
                    
                    <!-- Direção -->
                    <div class="form-group">
                        <label class="form-label" for="score_direction">
                            🎬 Direção
                        </label>
                        <input 
                            type="number" 
                            name="score_direction" 
                            id="score_direction" 
                            class="form-input"
                            min="0" 
                            max="10" 
                            step="0.1"
                            value="{{ old('score_direction', $review->score_direction ?? '') }}"
                        >
                    </div>
                    
                    <!-- Animação -->
                    <div class="form-group">
                        <label class="form-label" for="score_animation">
                            🎨 Animação
                        </label>
                        <input 
                            type="number" 
                            name="score_animation" 
                            id="score_animation" 
                            class="form-input"
                            min="0" 
                            max="10" 
                            step="0.1"
                            value="{{ old('score_animation', $review->score_animation ?? '') }}"
                        >
                    </div>
                    
                    <!-- Trilha Sonora -->
                    <div class="form-group">
                        <label class="form-label" for="score_soundtrack">
                            🎵 Trilha Sonora
                        </label>
                        <input 
                            type="number" 
                            name="score_soundtrack" 
                            id="score_soundtrack" 
                            class="form-input"
                            min="0" 
                            max="10" 
                            step="0.1"
                            value="{{ old('score_soundtrack', $review->score_soundtrack ?? '') }}"
                        >
                    </div>
                    
                    <!-- Impacto -->
                    <div class="form-group">
                        <label class="form-label" for="score_impact">
                            💥 Impacto Geral
                        </label>
                        <input 
                            type="number" 
                            name="score_impact" 
                            id="score_impact" 
                            class="form-input"
                            min="0" 
                            max="10" 
                            step="0.1"
                            value="{{ old('score_impact', $review->score_impact ?? '') }}"
                        >
                    </div>
                </div>
            </div>
            
            <!-- Notas -->
            <div class="stat-card" style="padding: var(--spacing-lg); margin-bottom: var(--spacing-md);">
                <div class="form-group">
                    <label class="form-label" for="notes">
                        📝 Notas e Comentários
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        class="form-textarea"
                        placeholder="Adicione suas observações sobre o anime..."
                    >{{ old('notes', $review->notes ?? '') }}</textarea>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: flex; align-items: center; gap: var(--spacing-xs); cursor: pointer;">
                        <input 
                            type="checkbox" 
                            name="is_published" 
                            value="1"
                            {{ old('is_published', $review->is_published ?? false) ? 'checked' : '' }}
                            style="width: 20px; height: 20px;"
                        >
                        <span class="form-label" style="margin: 0;">Publicar avaliação</span>
                    </label>
                </div>
            </div>
            
            <!-- Actions -->
            <div style="display: flex; gap: var(--spacing-sm); justify-content: space-between;">
                <div style="display: flex; gap: var(--spacing-sm);">
                    <button type="submit" class="btn btn-primary">
                        Salvar Avaliação
                    </button>
                    
                    <a href="{{ route('dashboard.show', $anime) }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
                
                @if($review)
                <form action="{{ route('reviews.destroy', $anime) }}" method="POST" 
                      onsubmit="return confirm('Tem certeza que deseja excluir esta avaliação?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost" style="color: #EF4444;">
                        Excluir Avaliação
                    </button>
                </form>
                @endif
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Auto-calculate final score preview
    const scoreInputs = document.querySelectorAll('input[type="number"]');
    
    scoreInputs.forEach(input => {
        input.addEventListener('input', updatePreview);
    });
    
    function updatePreview() {
        const scores = Array.from(scoreInputs)
            .map(input => parseFloat(input.value))
            .filter(score => !isNaN(score) && score > 0);
        
        if (scores.length > 0) {
            const average = scores.reduce((a, b) => a + b, 0) / scores.length;
            console.log('Nota final estimada:', average.toFixed(2));
        }
    }
</script>
@endpush
@endsection
