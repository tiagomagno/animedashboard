<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - AnimeDash</title>
    
    <!-- CSS Modular -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/filters.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/stats.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    @stack('styles')
    <style>
        :root {
            /* Palette Figma */
            --bg-primary: #0D0D0D;
            --bg-secondary: #0D0D0D; /* Mantém dark profundo */
            --bg-tertiary: #1a1a1a;
            
            --primary-color: #A7F205; /* Verde Neon */
            --primary-hover: #96D907;
            --primary-dark: #73A605;
            
            --text-primary: #FFFFFF;
            --text-secondary: #D9D9D9; /* Cinza claro */
            --text-muted: #888888;
            
            --border-color: #333333;
            --spacing-md: 1.5rem;
            --header-height: 80px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding-top: var(--header-height); /* ESSENCIAL: Empurra conteúdo para baixo do header fixo */
            overflow-x: hidden;
        }

        /* === HEADER ESTILO FIGMA (Transparente) === */
        .header {
            background: linear-gradient(180deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%);
            height: var(--header-height);
            display: flex;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            border-bottom: 0;
            pointer-events: none;
        }
        
        .header-content {
            pointer-events: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
        }

        /* Esquerda: Logo + Seletores */
        .header-left {
            display: flex;
            align-items: center;
        }

        .logo {
            font-family: 'League Spartan', sans-serif;
            font-weight: 800;
            font-size: 32px; /* Solicitado */
            color: #fff;
            text-decoration: none;
            letter-spacing: -1px;
            margin-right: 2rem;
            line-height: 1;
        }
        
        .logo span { color: var(--primary-color); }

        /* === YEAR SELECTOR (FIGMA STYLE) === */
        .year-selector-container {
            position: relative;
            margin-right: 2rem;
        }

        .year-selector-btn {
            position: relative;
            width: 140px;
            height: 40px;
            border: 1px solid #333;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            box-sizing: border-box;
        }
        
        .year-selector-btn:hover,
        .year-selector-container.active .year-selector-btn {
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 0.1);
        }

        /* Label "Ano" flutuante */
        .year-label-floating {
            position: absolute;
            top: -9px;
            left: 1rem;
            background: transparent; /* Truque: usar linear gradient local ou cor solida se background fosse solido */
            /* Como o header é transparente, precisamos de um fundo escuro pequeno atrás do texto ou apenas texto sobreposto */
            /* Hack: Background #0D0D0D igual o topo da página para cobrir a borda */
            background: #0D0D0D; 
            padding: 0 4px;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 500;
            z-index: 2;
            line-height: 1;
        }
        .year-selector-container.active .year-label-floating { color: var(--primary-color); }

        .year-dropdown {
            position: absolute;
            top: 45px;
            left: 0;
            width: 140px;
            background: #121212;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 0.5rem;
            display: none;
            flex-direction: column;
            gap: 2px;
            z-index: 200;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            box-sizing: border-box;
        }
        .year-selector-container.active .year-dropdown { display: flex; }

        .year-option {
            padding: 0.6rem 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: block;
        }
        .year-option:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .year-option.active {
            color: var(--primary-color);
            background: rgba(167, 242, 5, 0.1);
        }

        /* === MENU LINKS === */
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        .nav-link {
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        .nav-link:hover { color: #fff; }
        .nav-link.active { color: #fff; font-weight: 600; }

        /* Direita */
        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* === EXPANDABLE SEARCH === */
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .search-trigger {
            background: transparent;
            border: 1px solid #333;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 20;
            box-sizing: border-box;
        }
        
        .search-trigger:hover { border-color: #fff; }

        .search-expanded-wrapper {
            position: absolute;
            right: 0; 
            top: 0;
            width: 40px; 
            height: 40px;
            background: #fff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            overflow: hidden;
            transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s;
            opacity: 0;
            pointer-events: none;
            z-index: 10;
        }

        .search-container.open .search-expanded-wrapper {
            width: 300px;
            opacity: 1;
            pointer-events: auto;
        }
        
        /* Esconde trigger quando aberto */
        .search-container.open .search-trigger {
            opacity: 0;
            pointer-events: none;
            transform: scale(0.8);
        }

        .search-input-real {
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
            background: transparent;
            padding: 0 1rem 0 3rem; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 500;
            font-size: 0.9rem;
            color: #000;
            box-sizing: border-box;
        }

        .search-icon-fixed {
            position: absolute;
            left: 12px;
            color: #000;
            font-size: 1.1rem;
        }

        /* Botão Streamer (Círculo Verde) */
        .btn-streamer {
            background: var(--primary-color);
            color: #000;
            width: 40px; 
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            transition: transform 0.2s;
            box-sizing: border-box;
            pointer-events: auto;
        }
        
        .btn-streamer:hover {
            transform: scale(1.05);
            background: var(--primary-hover);
        }

        /* Botão Importar (Outline) */
        .btn-import-outline {
            height: 40px;
            border: 1px solid #333;
            border-radius: 9999px;
            padding: 0 1.2rem;
            background: rgba(0,0,0,0.6); 
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            box-sizing: border-box;
            pointer-events: auto;
        }
        
        .btn-import-outline:hover {
            border-color: var(--text-primary);
            color: var(--text-primary);
            background: rgba(0,0,0,0.8);
        }
        
        /* === ANIME CARD (DESIGN NOVO) === */
        .anime-card {
            background: var(--bg-tertiary) !important;
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #2a2a2a;
            display: flex;
            flex-direction: column;
            position: relative;
            height: 100%;
        }
        
        .anime-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            border-color: var(--primary-color);
        }

        .anime-card-image {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            display: block;
        }

        .anime-card-content {
            padding: 1rem;
            background: var(--bg-tertiary);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .anime-card-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
            color: #fff;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.3;
        }
        
        .anime-card-subtitle {
            font-size: 0.8rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.5rem;
        }
        
        .anime-card-meta {
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        .anime-score {
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--primary-color); 
            font-weight: 700; 
        }
        
        .anime-members {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-import-outline:hover {
            border-color: var(--text-primary);
            color: var(--text-primary);
        }

        /* Removemos estilos antigos que conflitam */
        .year-selector, .dropdown { display: none !important; } 

        /* ... (Resto do CSS do Modal/Conteúdo mantido) ... */

        /* Settings Modal */
        .settings-modal {
            max-width: 500px;
            width: 90%;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            display: flex; /* Garante flex column */
            flex-direction: column;
            gap: 1rem;
            padding: 2rem; /* Mais espaçamento interno */
        }

        /* Garante que o header fique no topo */
        .settings-modal .modal-header-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 0.5rem;
        }

        /* Título do modal */
        .settings-modal .modal-title {
            font-size: 1.5rem;
            margin: 0;
        }

        /* Grid de opções limpo */
        .camera-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            width: 100%;
        }

        .camera-option {
            background: var(--bg-tertiary);
            padding: 1.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-align: center;
        }
        
        .camera-option span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .camera-option:hover {
            border-color: var(--primary-color, #ef4444);
            background: rgba(239, 68, 68, 0.1);
        }

        .camera-option.active {
            border-color: var(--primary-color, #ef4444);
            background: rgba(239, 68, 68, 0.15);
        }

        .screen-preview {
            width: 80px; /* Levemente menor para caber melhor */
            height: 45px;
            background: #121212; /* Fundo mais escuro para contraste */
            border: 1px solid #333;
            border-radius: 4px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .cam-preview {
            width: 24px;
            height: 24px;
            background: #ef4444;
            position: absolute;
            border-radius: 2px;
            opacity: 0.9;
        }
        
        /* Posições do Preview */
        .cam-preview.top-left { top: 2px; left: 2px; }
        .cam-preview.top-right { top: 2px; right: 2px; }
        .cam-preview.bottom-left { bottom: 2px; left: 2px; }
        .cam-preview.bottom-right { bottom: 2px; right: 2px; }
        .cam-preview.center-left { top: 50%; left: 2px; transform: translateY(-50%); }
        .cam-preview.center-right { top: 50%; right: 2px; transform: translateY(-50%); }
        .cam-preview.off { width: 100%; height: 100%; background: transparent; display: flex; align-items: center; justify-content: center; color: #666; font-size: 1.5rem; }

        /* === NOVO LAYOUT CENTRALIZADO (ESTILO YOUTUBE/NETFLIX) === */
        
        /* Por padrão (sem modo streamer), o site aproveita bem a largura */
        .header-content,
        .main-content {
            max-width: 1600px; /* Largura generosa padrão */
            margin: 0 auto;
            padding: 0 2rem;
            transition: max-width 0.3s ease, margin 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        /* Quando o Modo Streamer está ATIVO (Qualquer posição) */
        body[class*="has-cam-"] .header-content,
        body[class*="has-cam-"] .main-content {
            /* Restringimos a largura para garantir margens laterais largas */
            max-width: 1100px; 
            margin: 0 auto; /* Mantém centralizado por padrão */
        }

        /* Se a câmera for ESQUERDA (Qualquer altura) */
        body[class*="has-cam-"][class*="-left"] .header-content,
        body[class*="has-cam-"][class*="-left"] .main-content {
            /* Se quiser levemente deslocado para a direita, descomente abaixo. 
               Mas 'margin: 0 auto' com max-width menor já resolve a maioria dos casos. 
            */
             margin-left: auto; 
             margin-right: 2rem; /* Cola mais na direita para fugir da esquerda */
        }

        /* Se a câmera for DIREITA (Qualquer altura) */
        body[class*="has-cam-"][class*="-right"] .header-content,
        body[class*="has-cam-"][class*="-right"] .main-content {
            margin-right: auto;
            margin-left: 2rem; /* Cola mais na esquerda para fugir da direita */
        }
        
        /* Se for TOP, precisamos baixar o header e content */
        body.has-cam-top-left .header,
        body.has-cam-top-right .header {
            padding-top: 140px;
        }
        
        /* Se for BOTTOM, precisamos de espaço embaixo */
        body.has-cam-bottom-left .main-content,
        body.has-cam-bottom-right .main-content {
            padding-bottom: 200px;
        }

        /* Responsividade */
        @media (max-width: 1200px) {
            body[class*="has-cam-"] .header-content,
            body[class*="has-cam-"] .main-content {
                max-width: 100%;
                padding: 0 var(--spacing-md);
                margin: 0; /* Reseta margens em telas menores */
            }
            body.has-cam-top-left .header,
            body.has-cam-top-right .header {
                padding-top: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <!-- Left: Logo + Year + Menu -->
            <div class="header-left">
                <a href="{{ route('dashboard.index') }}" class="logo">
                    anime<span>dash</span>
                </a>
                
                <!-- Year Selector (Figma Style) -->
                <div class="year-selector-container" id="yearSelectorContainer">
                    <span class="year-label-floating">Ano</span>
                    <div class="year-selector-btn" onclick="toggleYearMenu()">
                        <span>{{ $year ?? date('Y') }}</span>
                        <i class="ph ph-caret-down"></i>
                    </div>
                    
                    <div class="year-dropdown">
                        @foreach($availableYears as $y)
                            <a href="#" onclick="updateYear('{{ $y }}')" class="year-option {{ (isset($year) && $y == $year) ? 'active' : '' }}">
                                {{ $y }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Nav Menu -->
                <nav class="nav-links">
                    <a href="{{ route('dashboard.index') }}" class="nav-link">Dashboard</a>
                    <a href="{{ route('rankings.index') }}" class="nav-link">Rankings</a>
                    <a href="{{ route('calendar.index') }}" class="nav-link">Calendário</a>
                </nav>
            </div>
            
            <!-- Right: Search + Streamer + Import -->
            <div class="header-right">
                <!-- Expandable Search -->
                <div class="search-container" id="searchContainer">
                    <button class="search-trigger" onclick="openSearch()">
                        <i class="ph ph-magnifying-glass" style="font-size: 1.2rem;"></i>
                    </button>
                    
                    <div class="search-expanded-wrapper">
                        <i class="ph ph-magnifying-glass search-icon-fixed"></i>
                        <input 
                            type="text" 
                            class="search-input-real" 
                            placeholder="Buscar anime" 
                            id="animeSearch"
                            autocomplete="off"
                            onblur="closeSearchIfEmpty()"
                        >
                    </div>
                </div>

                <button class="btn-streamer" onclick="toggleSettingsModal()" title="Modo Streamer">
                    <i class="ph-fill ph-monitor-arrow-up"></i>
                </button>
                
                <a href="{{ route('seasons.index') }}" class="btn-import-outline">
                    <i class="ph ph-download-simple"></i> Importar animes
                </a>
            </div>
        </div>
    </header>

    <!-- Streamer Mode Settings Modal -->
    <div class="modal-overlay" id="settingsModal" onclick="closeSettingsModal(event)">
        <div class="modal-content settings-modal" onclick="event.stopPropagation()">
            <div class="modal-header-group">
                <h2 class="modal-title">Modo Streamer</h2>
                <button class="modal-close" onclick="closeSettingsModal(event)">
                    <i class="ph ph-x"></i>
                </button>
            </div>
            
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
                Selecione a posição da sua câmera para ajustar o layout e evitar que informações sejam cobertas.
            </p>

            <div class="camera-grid">
                <!-- Top Left -->
                <div class="camera-option" onclick="setCameraPosition('top-left')" data-pos="top-left">
                    <div class="screen-preview">
                        <div class="cam-preview top-left"></div>
                    </div>
                    <span>Canto Sup. Esq.</span>
                </div>

                <!-- Top Right -->
                <div class="camera-option" onclick="setCameraPosition('top-right')" data-pos="top-right">
                    <div class="screen-preview">
                        <div class="cam-preview top-right"></div>
                    </div>
                    <span>Canto Sup. Dir.</span>
                </div>

                <!-- Center Left -->
                <div class="camera-option" onclick="setCameraPosition('center-left')" data-pos="center-left">
                    <div class="screen-preview">
                        <div class="cam-preview center-left"></div>
                    </div>
                    <span>Esquerda Centro</span>
                </div>

                <!-- Center Right -->
                <div class="camera-option" onclick="setCameraPosition('center-right')" data-pos="center-right">
                    <div class="screen-preview">
                        <div class="cam-preview center-right"></div>
                    </div>
                    <span>Direita Centro</span>
                </div>

                <!-- Bottom Left -->
                <div class="camera-option" onclick="setCameraPosition('bottom-left')" data-pos="bottom-left">
                    <div class="screen-preview">
                        <div class="cam-preview bottom-left"></div>
                    </div>
                    <span>Canto Inf. Esq.</span>
                </div>

                <!-- Bottom Right -->
                <div class="camera-option" onclick="setCameraPosition('bottom-right')" data-pos="bottom-right">
                    <div class="screen-preview">
                        <div class="cam-preview bottom-right"></div>
                    </div>
                    <span>Canto Inf. Dir.</span>
                </div>

                <!-- OFF -->
                <div class="camera-option" onclick="setCameraPosition('off')" data-pos="off" style="grid-column: 1 / -1;">
                    <div class="screen-preview">
                        <div class="cam-preview off"><i class="ph ph-video-camera-slash"></i></div>
                    </div>
                    <span>Modo Normal (Sem Câmera)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Floating Calendar -->
    <div id="bottomCalendar" class="bottom-calendar-container closed">
        <div class="calendar-header" onclick="toggleBottomCalendar()">
            <div class="calendar-title" style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph ph-calendar-blank"></i> Calendário Semanal
            </div>
            <i class="ph ph-caret-up toggle-icon"></i>
        </div>
        <div class="calendar-content">
            <div class="calendar-days">
                @php $days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo']; @endphp
                @foreach($days as $day)
                    <div class="day-column">
                        <div class="day-header">{{ $day }}</div>
                        <div class="day-animes">
                            <div class="empty-day-state">Sem dados</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        /* Bottom Calendar Styles */
        .bottom-calendar-container {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 1600px;
            background: #121212;
            border-top: 1px solid #333;
            border-left: 1px solid #333;
            border-right: 1px solid #333;
            border-radius: 16px 16px 0 0;
            z-index: 900;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 -10px 40px rgba(0,0,0,0.5);
        }

        .bottom-calendar-container.closed {
            transform: translate(-50%, calc(100% - 40px));
        }
        
        .calendar-header {
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            cursor: pointer;
            background: var(--primary-color);
            color: #000;
            border-radius: 14px 14px 0 0;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .toggle-icon { transition: transform 0.3s; }
        .bottom-calendar-container:not(.closed) .toggle-icon { transform: rotate(180deg); }

        .calendar-content {
            height: 300px;
            padding: 1rem;
            overflow-y: auto;
            background: #121212;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1rem;
            height: 100%;
        }

        .day-column {
            background: var(--bg-tertiary);
            border-radius: 8px;
            padding: 0.5rem;
            display: flex;
            flex-direction: column;
        }

        .day-header {
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .empty-day-state {
            text-align: center;
            font-size: 0.8rem;
            color: #444;
            margin-top: 2rem;
        }
    </style>

    <script>
        // === HEADER: YEAR MENU ===
        function toggleYearMenu() {
            const container = document.getElementById('yearSelectorContainer');
            if(container) container.classList.toggle('active');
        }
        
        document.addEventListener('click', (e) => {
            const yearContainer = document.getElementById('yearSelectorContainer');
            if (yearContainer && !yearContainer.contains(e.target)) {
                yearContainer.classList.remove('active');
            }
        });

        function updateYear(year) {
            const url = new URL(window.location.href);
            url.searchParams.set('year', year);
            url.searchParams.delete('season'); // Reset season on year change usually
            window.location.href = url.toString();
        }

        // === HEADER: EXPANDABLE SEARCH ===
        function openSearch() {
            const container = document.getElementById('searchContainer');
            const input = document.getElementById('animeSearch');
            if(container) {
                container.classList.add('open');
                setTimeout(() => input && input.focus(), 100);
            }
        }

        function closeSearchIfEmpty() {
            const input = document.getElementById('animeSearch');
            const container = document.getElementById('searchContainer');
            if (input && !input.value.trim() && container) {
                container.classList.remove('open');
            }
        }
        
        // Search Filter (Client Side Simple)
        const searchInput = document.getElementById('animeSearch');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const query = e.target.value.toLowerCase().trim();
                
                searchTimeout = setTimeout(() => {
                    const cards = document.querySelectorAll('.anime-card');
                    cards.forEach(card => {
                        const title = card.dataset.title?.toLowerCase() || '';
                        const altTitle = card.dataset.englishTitle?.toLowerCase() || '';
                        if (title.includes(query) || altTitle.includes(query)) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }, 300);
            });
        }

        // === CALENDAR LOGIC ===
        function toggleBottomCalendar(e) {
            if(e) e.preventDefault();
            const cal = document.getElementById('bottomCalendar');
            if(cal) cal.classList.toggle('closed');
        }

        // === STREAMER MODE LOGIC ===
        function toggleSettingsModal() {
            const modal = document.getElementById('settingsModal');
            if(modal) {
                modal.style.display = (modal.style.display === 'flex' || modal.classList.contains('active')) ? 'none' : 'flex';
                modal.classList.toggle('active');
                
                const currentPos = localStorage.getItem('streamerCamPos') || 'off';
                updateActiveCameraOption(currentPos);
            }
        }

        function closeSettingsModal(event) {
            if (event) event.stopPropagation();
            const modal = document.getElementById('settingsModal');
            if(modal) {
                modal.style.display = 'none';
                modal.classList.remove('active');
            }
        }

        function setCameraPosition(pos) {
            document.body.classList.remove(
                'has-cam-top-left', 'has-cam-top-right',
                'has-cam-bottom-left', 'has-cam-bottom-right',
                'has-cam-center-left', 'has-cam-center-right'
            );
            
            if (pos !== 'off') {
                document.body.classList.add(`has-cam-${pos}`);
            }
            
            localStorage.setItem('streamerCamPos', pos);
            updateActiveCameraOption(pos);
        }

        function updateActiveCameraOption(pos) {
            document.querySelectorAll('.camera-option').forEach(el => {
                el.classList.remove('active');
                if (el.dataset.pos === pos) {
                    el.classList.add('active');
                }
            });
        }

        // === GLOBAL INIT ===
        document.addEventListener('DOMContentLoaded', () => {
            // Restore Camera
            const savedPos = localStorage.getItem('streamerCamPos');
            if (savedPos) {
                setCameraPosition(savedPos);
            }
            
            // Auto-hide alerts
            document.querySelectorAll('.alert').forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.3s';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });

        // Toggle Filter Helper (used in Dashboard Index)
        function toggleFilter(filterName, isChecked) {
            const url = new URL(window.location.href);
            url.searchParams.set(filterName, isChecked ? '1' : '0');
            window.location.href = url.toString();
        }
    </script>

    <!-- Calendar Floating Widget -->
    <x-calendar-floating />

    @stack('scripts')
</body>
</html>
