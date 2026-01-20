# 📋 PRÓXIMAS IMPLEMENTAÇÕES - AnimeDashboard

## ✅ Solicitações do Usuário:

### 1. **Filtros de Classificação Etária**
- [ ] Adicionar campo `rating` na tabela animes
- [ ] Checkbox para filtrar Kids (G, PG)
- [ ] Checkbox para filtrar +18 (R+, Rx)
- [ ] Atualizar config/mal.php para incluir campo `rating`
- [ ] Atualizar SeasonImportService para salvar rating

### 2. **Melhorias nos Cards**
- [ ] Adicionar número de membros ao lado do score
- [ ] Formato: ⭐ 8.50 | 👥 1.2M

### 3. **Menu de Rankings (Top 5)**
- [ ] Top 10 Score MAL
- [ ] Top 10 Popularidade
- [ ] Top 10 Membros
- [ ] Top 10 Score Editorial
- [ ] Top 10 Mais Recentes
- [ ] Criar dropdown menu no header

### 4. **Reestruturação do Header**
**Lado Esquerdo:**
- Logo ANIMEDASH (link para home)
- Seletor de Ano
- Busca

**Lado Direito:**
- Menu de Rankings (dropdown)
- Botão "Importar" (vermelho, destaque)

### 5. **Página de Importação**
- [ ] Simplificar layout com boxes
- [ ] Melhorar visualização das temporadas importadas
- [ ] Usar cards em grid ao invés de lista

## 📝 Arquivos a Modificar:

1. `database/migrations/*_add_rating_to_animes_table.php` ✅
2. `config/mal.php` - adicionar 'rating' aos fields
3. `app/Models/Anime.php` - adicionar 'rating' ao fillable
4. `app/Services/SeasonImportService.php` - salvar rating
5. `app/Http/Controllers/DashboardController.php` - adicionar filtros e rankings
6. `resources/views/layouts/app.blade.php` - novo header
7. `resources/views/dashboard/index.blade.php` - checkboxes e membros nos cards
8. `resources/views/seasons/index.blade.php` - simplificar layout
9. `public/css/app.css` - estilos para novos elementos
10. `routes/web.php` - rotas para rankings

## 🎯 Status Atual:

- ✅ Migration criada para campo `rating`
- ⏳ Aguardando execução das demais implementações

## 💡 Próximo Passo:

Executar `php artisan migrate` e continuar com as implementações restantes.
