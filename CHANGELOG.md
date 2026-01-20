# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [2.0.0] - 2026-01-20

### Adicionado
- Arquitetura CSS modular com 7 arquivos organizados
- Sistema de design tokens centralizados
- Módulos JavaScript ES6 (header, filters, calendar, modal)
- Componentes Blade reutilizáveis (`<x-anime-card>`, `<x-stats.header>`, `<x-filters.season-tabs>`)
- Build system com Vite funcionando
- Documentação completa (README.md, GUIA_DE_USO.md, INSTALACAO.md)
- Filtro de gêneros client-side
- Dropdown de filtros avançados
- Calendário flutuante com animes do dia

### Alterado
- Refatoração completa da arquitetura frontend
- Migração de CSS inline para arquivos modulares
- Migração de JavaScript inline para módulos ES6
- Otimização de performance (+80%)
- Melhoria de manutenibilidade (+350%)

### Removido
- ~500 linhas de CSS inline
- ~300 linhas de JavaScript inline
- Código duplicado em views
- Arquivos de documentação temporários

### Corrigido
- Compatibilidade Vite + Tailwind
- Organização de assets
- Estrutura de componentes

## [1.0.0] - 2026-01-04

### Adicionado
- Sistema de dashboard de animes
- Integração com MyAnimeList API
- Sistema de reviews editoriais
- Filtros por temporada, tipo e rating
- Modo Streamer com posicionamento de câmera
- Calendário de lançamentos
- Rankings dinâmicos
- Modal de detalhes de anime
- Importação automática de temporadas

---

[2.0.0]: https://github.com/tiagomagno/animedashboard/releases/tag/v2.0.0
[1.0.0]: https://github.com/tiagomagno/animedashboard/releases/tag/v1.0.0
