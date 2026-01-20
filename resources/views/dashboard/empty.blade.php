@extends('layouts.app')

@section('title', 'Bem-vindo')

@section('content')
<div class="container">
    <div class="hero" style="text-align: center; padding: var(--spacing-xl) 0;">
        <div class="hero-content">
            <h1 class="hero-title" style="font-size: 4rem; margin-bottom: var(--spacing-md);">
                Bem-vindo ao AnimeDash
            </h1>
            <p class="hero-subtitle" style="font-size: 1.5rem; margin-bottom: var(--spacing-xl);">
                Dashboard de análise editorial de temporadas de anime
            </p>
            
            <div class="alert alert-info" style="max-width: 600px; margin: 0 auto var(--spacing-xl) auto;">
                <p style="margin: 0;">
                    Você ainda não importou nenhuma temporada. 
                    Comece importando sua primeira temporada para começar a análise!
                </p>
            </div>
            
            <a href="{{ route('seasons.index') }}" class="btn btn-primary" style="font-size: 1.25rem; padding: var(--spacing-md) var(--spacing-xl);">
                Importar Primeira Temporada
            </a>
        </div>
    </div>
    
    <div class="section">
        <h2 class="section-title text-center">✨ Recursos</h2>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div style="font-size: 3rem; margin-bottom: var(--spacing-sm);">📊</div>
                <h3 style="margin-bottom: var(--spacing-xs);">Métricas MAL</h3>
                <p class="text-muted">Importação automática de dados do MyAnimeList com histórico temporal</p>
            </div>
            
            <div class="stat-card">
                <div style="font-size: 3rem; margin-bottom: var(--spacing-sm);">⭐</div>
                <h3 style="margin-bottom: var(--spacing-xs);">Avaliações Editoriais</h3>
                <p class="text-muted">Sistema completo de avaliação com critérios personalizados</p>
            </div>
            
            <div class="stat-card">
                <div style="font-size: 3rem; margin-bottom: var(--spacing-sm);">🏆</div>
                <h3 style="margin-bottom: var(--spacing-xs);">Rankings</h3>
                <p class="text-muted">Rankings automáticos por score, popularidade e avaliação editorial</p>
            </div>
            
            <div class="stat-card">
                <div style="font-size: 3rem; margin-bottom: var(--spacing-sm);">📈</div>
                <h3 style="margin-bottom: var(--spacing-xs);">Análise Temporal</h3>
                <p class="text-muted">Acompanhe a evolução dos animes ao longo da temporada</p>
            </div>
        </div>
    </div>
</div>
@endsection
