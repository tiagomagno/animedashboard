@extends('layouts.app')

@section('title', 'Gerenciar Temporadas')

@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; margin-bottom: var(--spacing-xs);">Gerenciar Temporadas</h1>
            <p style="color: var(--text-secondary);">Gerencie e atualize seus dados de animes.</p>
        </div>
    </div>

    <!-- Seção: Nova Importação -->
    <div style="background: var(--bg-card); padding: var(--spacing-lg); border-radius: var(--radius-lg); border: 1px solid var(--border); margin-bottom: var(--spacing-2xl);">
        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: var(--spacing-md); display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-plus-circle" style="font-size: 1.25rem; color: var(--primary);"></i>
            Importar Novo Ano
        </h3>
        
        <form action="{{ route('seasons.import-year') }}" method="POST" style="display: flex; gap: var(--spacing-md); align-items: center;">
            @csrf
            <select name="year" class="form-select" style="flex: 1; max-width: 200px;">
                @for($year = now()->year + 1; $year >= 2000; $year--)
                    <option value="{{ $year }}" {{ $year === now()->year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endfor
            </select>
            <button type="submit" class="btn-import">
                <i class="ph ph-download-simple"></i> Importar Ano Completo
            </button>
            <span style="font-size: 0.8rem; color: var(--text-muted);">
                Isso importará as 4 temporadas (Winter, Spring, Summer, Fall).
            </span>
        </form>
    </div>

    <!-- Lista Consolidada -->
    <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--border);">
        <i class="ph ph-database"></i> Dados Importados
    </h2>

    @if($importedSeasons->count() > 0)
        @php
            $seasonsByYear = $importedSeasons->groupBy('year')->sortByDesc(function($seasons, $year) {
                return $year;
            });
        @endphp

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: var(--spacing-lg);">
            @foreach($seasonsByYear as $year => $seasons)
                <div class="year-card" style="background: var(--bg-card); border-radius: var(--radius-md); border: 1px solid var(--border); padding: var(--spacing-lg); display: flex; flex-direction: column; gap: var(--spacing-md); transition: transform 0.2s, box-shadow 0.2s;">
                    
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">{{ $year }}</h3>
                        
                        <form action="{{ route('seasons.import-year') }}" method="POST">
                            @csrf
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="force" value="1">
                            <button type="submit" class="btn-icon" title="Atualizar dados deste ano" style="background: transparent; border: none; color: var(--text-muted); cursor: pointer; padding: 0.25rem; border-radius: 4px; transition: all 0.2s;">
                                <i class="ph ph-arrows-clockwise" style="font-size: 1.25rem;"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Seasons Badge Grid -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        @foreach(['winter', 'spring', 'summer', 'fall'] as $seasonKey)
                            @php
                                $season = $seasons->where('season', $seasonKey)->first();
                                $color = match($seasonKey) {
                                    'winter' => '#3b82f6',
                                    'spring' => '#22c55e',
                                    'summer' => '#eab308',
                                    'fall' => '#f97316',
                                    default => '#888888'
                                };
                            @endphp
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; border-radius: 6px; background: {{ $season ? 'var(--bg-tertiary)' : 'transparent' }}; border: 1px solid {{ $season ? 'transparent' : 'var(--border)' }}; opacity: {{ $season ? 1 : 0.5 }};">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $color }};"></div>
                                    <span style="font-size: 0.875rem; color: var(--text-secondary); text-transform: capitalize;">{{ $seasonKey }}</span>
                                </div>
                                @if($season)
                                    <strong style="font-size: 0.875rem; color: var(--text-primary);">{{ $season->animes->count() }}</strong>
                                @else
                                    <span style="font-size: 0.875rem; color: var(--text-muted);">-</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon"><i class="ph ph-folder-dashed"></i></div>
            <h2 class="empty-state-title">Nenhum dado importado</h2>
            <p>Comece importando um ano completo acima.</p>
        </div>
    @endif
</div>
@endsection
