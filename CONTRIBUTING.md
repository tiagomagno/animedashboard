# Contribuindo para AnimeDashboard

Obrigado por considerar contribuir para o AnimeDashboard! 🎉

## Como Contribuir

### Reportar Bugs

Se você encontrou um bug, por favor abra uma [issue](https://github.com/tiagomagno/animedashboard/issues) incluindo:

- Descrição clara do problema
- Passos para reproduzir
- Comportamento esperado vs atual
- Screenshots (se aplicável)
- Ambiente (OS, PHP version, etc)

### Sugerir Melhorias

Sugestões são bem-vindas! Abra uma issue com:

- Descrição clara da melhoria
- Justificativa (por que seria útil)
- Exemplos de uso (se aplicável)

### Pull Requests

1. **Fork** o repositório
2. **Clone** seu fork: `git clone https://github.com/SEU-USUARIO/animedashboard.git`
3. **Crie uma branch**: `git checkout -b feature/minha-feature`
4. **Faça suas alterações**
5. **Commit** seguindo [Conventional Commits](#conventional-commits)
6. **Push**: `git push origin feature/minha-feature`
7. **Abra um Pull Request**

## Conventional Commits

Usamos [Conventional Commits](https://www.conventionalcommits.org/pt-br/) para mensagens de commit:

```
feat: adiciona filtro de estúdios
fix: corrige bug no modal de detalhes
docs: atualiza README com instruções de deploy
style: formata código com Pint
refactor: reorganiza estrutura de Services
test: adiciona testes para AnimeFilterService
chore: atualiza dependências
```

## Padrões de Código

### PHP
- Seguir [PSR-12](https://www.php-fig.org/psr/psr-12/)
- Usar Laravel Pint: `./vendor/bin/pint`
- Documentar métodos complexos

### JavaScript
- ES6+ modules
- Comentários JSDoc para funções públicas
- Evitar poluição do escopo global

### CSS
- Usar variáveis CSS (design tokens)
- Nomenclatura BEM-like
- Mobile-first approach

### Blade
- Preferir componentes a partials
- Props tipadas quando possível
- Evitar lógica complexa nas views

## Estrutura de Branches

- `main` - Produção (protegida)
- `develop` - Desenvolvimento
- `feature/*` - Novas funcionalidades
- `fix/*` - Correções de bugs
- `docs/*` - Documentação

## Testes

```bash
# Rodar testes
php artisan test

# Com coverage
php artisan test --coverage
```

## Checklist do PR

- [ ] Código segue os padrões do projeto
- [ ] Testes adicionados/atualizados
- [ ] Documentação atualizada
- [ ] Commit messages seguem Conventional Commits
- [ ] Build passa sem erros
- [ ] Sem conflitos com `main`

## Dúvidas?

Abra uma [Discussion](https://github.com/tiagomagno/animedashboard/discussions) ou entre em contato!

Obrigado! ❤️
