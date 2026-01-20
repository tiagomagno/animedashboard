# 📋 Guia de Uso - AnimeDashboard

## 🔄 Banco de Dados Resetado

O banco de dados foi resetado e está pronto para novas importações.

## 🚀 Fluxo de Trabalho Recomendado

### 1. **Importar Ano Completo** (Recomendado)

A forma mais eficiente é importar o ano completo de uma vez:

**Via Interface:**
1. Acesse: http://localhost:8000/seasons
2. Na seção "Importar Ano Completo"
3. Selecione o ano (ex: 2025)
4. Clique em "Importar Ano Completo (4 Temporadas)"
5. Aguarde (pode levar alguns minutos)

**Via Terminal:**
```bash
php artisan anime:import-year 2025
```

**O que acontece:**
- Importa Winter, Spring, Summer e Fall automaticamente
- **Pula temporadas já importadas** (evita duplicação)
- Mostra mensagem informando quais foram puladas

### 2. **Importar Temporada Individual** (Casos Específicos)

Use apenas se quiser importar uma temporada específica:

**Via Interface:**
1. Acesse: http://localhost:8000/seasons
2. Na seção "Importar Temporada Individual"
3. Selecione ano e temporada
4. Clique em "Importar Temporada"

**Quando usar:**
- Atualizar uma temporada específica
- Importar apenas uma temporada que falta
- Testar importação de uma temporada

### 3. **Navegar no Dashboard**

Após importar:

1. Acesse: http://localhost:8000
2. **Selecione o Ano** no dropdown
3. **Clique na Tab da Temporada** (All Seasons, Winter, Spring, Summer, Fall)
4. **Use os Filtros de Tipo**: All, TV, ONA, OVA, Movie, Special
5. Veja os rankings e animes filtrados!

## 📊 Estrutura de Dados

### O que é importado de cada anime:

**Dados Básicos:**
- ID do MyAnimeList
- Título
- Sinopse
- Imagem (medium e large)

**Métricas:**
- Score médio (mean)
- Número de membros (num_list_users)
- Popularidade (popularity)
- Rank

**Informações Adicionais:**
- Status (finished_airing, currently_airing, not_yet_aired)
- Tipo de mídia (tv, movie, ova, special, ona, music)
- Número de episódios
- Gêneros

## ⚠️ Importante

### Sobre Duplicações:

- ✅ **Importação de Ano** pula temporadas já importadas
- ✅ **Animes duplicados** são atualizados (não criados novamente)
- ✅ **MAL ID único** garante que cada anime existe apenas uma vez

### Sobre Temporadas:

- Cada anime pertence a **uma temporada específica**
- A API do MyAnimeList define em qual temporada o anime aparece
- Alguns animes podem aparecer em múltiplas temporadas (continuações)
- O sistema importa conforme a API retorna

## 🎯 Exemplo de Uso Completo

### Cenário 1: Começar do Zero

```bash
# 1. Importar 2025 completo
php artisan anime:import-year 2025

# 2. Importar 2024 completo
php artisan anime:import-year 2024

# 3. Acessar dashboard
# http://localhost:8000
```

### Cenário 2: Importação Seletiva

```bash
# 1. Importar apenas Winter 2025
# Via interface: Seasons > Importar Temporada Individual

# 2. Depois importar o resto do ano
php artisan anime:import-year 2025
# Resultado: "Skipped already imported seasons: Winter"
```

### Cenário 3: Atualizar Dados

```bash
# Atualizar stats de todas as temporadas ativas
php artisan anime:update-stats

# Ou atualizar temporada específica via interface:
# Seasons > [Temporada] > Atualizar Stats
```

## 📈 Comandos Úteis

```bash
# Importar ano completo
php artisan anime:import-year 2025

# Atualizar stats
php artisan anime:update-stats

# Verificar dados
php artisan anime:check

# Testar API
php artisan mal:test

# Limpar cache
php artisan cache:clear

# Resetar banco (CUIDADO: apaga tudo!)
php artisan migrate:fresh
```

## 🎨 Filtros Disponíveis

### No Dashboard:

1. **Por Ano**: Dropdown no topo
2. **Por Temporada**: Tabs (All Seasons, Winter, Spring, Summer, Fall)
3. **Por Tipo de Mídia**: Botões (All, TV, ONA, OVA, Movie, Special)

### Combinações:

- Ver todos os animes de 2025: `?year=2025&season=all`
- Ver apenas TV de Winter 2025: `?year=2025&season=winter&type=tv`
- Ver apenas Movies de 2024: `?year=2024&season=all&type=movie`

## 🔧 Troubleshooting

### "0 animes importados"
- Verifique se o Client ID está correto no `.env`
- Execute: `php artisan mal:test`
- Limpe o cache: `php artisan cache:clear`

### "Temporada já importada"
- Normal! O sistema evita duplicações
- Para reimportar, delete a temporada primeiro

### "Erro de conexão"
- Verifique sua internet
- A API do MAL pode estar temporariamente indisponível
- Tente novamente em alguns minutos

## ✅ Status Atual

- ✅ Banco de dados resetado
- ✅ Pronto para novas importações
- ✅ Sistema de prevenção de duplicações ativo
- ✅ Filtros por ano, temporada e tipo funcionando

**Pronto para começar! 🎌**
