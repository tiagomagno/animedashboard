# 🎬 AnimeDashboard

> Sistema editorial de análise de temporadas de anime com integração à API do MyAnimeList

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

![AnimeDashboard Preview](docs/preview.png)

---

## 📋 Sobre o Projeto

AnimeDashboard é uma plataforma web para análise editorial de temporadas de anime, permitindo:

- 📊 **Importação automática** de dados do MyAnimeList por temporada
- 🎯 **Filtros avançados** por tipo, gênero, rating e temporada
- ⭐ **Sistema de reviews** editoriais com scoring personalizado
- 📈 **Rankings dinâmicos** por score, popularidade e membros
- 📅 **Calendário semanal** de lançamentos
- 🎥 **Modo Streamer** com posicionamento de câmera configurável

---

## 🚀 Tecnologias

### Backend
- **Laravel 11** - Framework PHP
- **MySQL** - Banco de dados
- **MyAnimeList API** - Fonte de dados

### Frontend
- **Blade Components** - Templating modular
- **Vite** - Build tool
- **Vanilla JS (ES6 Modules)** - JavaScript modular
- **CSS Modules** - Arquitetura CSS escalável
- **Phosphor Icons** - Iconografia

### Design
- **Plus Jakarta Sans** - Tipografia principal
- **League Spartan** - Tipografia de destaque
- Paleta de cores neon (Verde #A7F205)
- Dark mode nativo

---

## 📦 Instalação

### Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+

### Passo a Passo

1. **Clone o repositório**
```bash
git clone https://github.com/seu-usuario/animedashboard.git
cd animedashboard
```

2. **Instale as dependências**
```bash
composer install
npm install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
Edite o `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=animedashboard
DB_USERNAME=root
DB_PASSWORD=
```

5. **Configure a API do MAL**
Obtenha suas credenciais em [MyAnimeList API](https://myanimelist.net/apiconfig) e adicione ao `.env`:
```env
MAL_CLIENT_ID=seu_client_id_aqui
```

6. **Execute as migrations**
```bash
php artisan migrate
```

7. **Compile os assets**
```bash
npm run build
# ou para desenvolvimento:
npm run dev
```

8. **Inicie o servidor**
```bash
php artisan serve
```

Acesse: `http://localhost:8000`

---

## 🎯 Funcionalidades

### Dashboard Principal
- Visualização de animes por temporada
- Estatísticas em tempo real (total, médias, scores)
- Filtros por tipo de mídia (TV, ONA, OVA, Movie, Special)
- Filtro de gêneros (client-side)
- Ocultação de conteúdo Kids/+18

### Sistema de Reviews
- Score editorial de 0-10
- Categorias: Narrativa, Animação, Som, Personagens
- Comentários e análises detalhadas

### Rankings
- Top 100 por Score MAL
- Top 100 por Popularidade
- Top 100 por Membros
- Top 100 Editorial

### Modo Streamer
- 6 posições de câmera configuráveis
- Layout adaptativo
- Salva preferências no localStorage

### Calendário
- Widget flutuante com animes do dia
- Página dedicada com grade semanal
- Informações de broadcast

---

## 🏗️ Arquitetura

### Estrutura de Pastas
```
animedashboard/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Services/          # Lógica de negócio
│   └── View/Components/   # Componentes Blade
├── resources/
│   ├── css/
│   │   ├── variables.css
│   │   ├── base.css
│   │   └── components/    # CSS modular
│   ├── js/
│   │   ├── app.js
│   │   └── modules/       # JS modular
│   └── views/
│       └── components/    # Blade components
├── public/
│   └── build/             # Assets compilados
└── routes/
    └── web.php
```

### Design Patterns
- **Repository Pattern** - Abstração de dados
- **Service Layer** - Lógica de negócio
- **Component-Based UI** - Blade components
- **Module Pattern** - JavaScript ES6

---

## 🎨 Guia de Estilo

### Cores
```css
--primary-color: #A7F205;  /* Verde Neon */
--bg-primary: #0D0D0D;     /* Preto profundo */
--text-primary: #FFFFFF;   /* Branco */
--text-muted: #888888;     /* Cinza */
```

### Tipografia
- **Headings**: League Spartan (800)
- **Body**: Plus Jakarta Sans (400-700)
- **Code**: Monospace

---

## 📚 Documentação

- [Guia de Uso](GUIA_DE_USO.md)
- [Instalação Detalhada](INSTALACAO.md)
- [Diagnóstico Técnico](DIAGNOSTICO_TECNICO.md)
- [Refatoração](REFATORACAO_RESUMO.md)

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch (`git checkout -b feature/NovaFeature`)
3. Commit suas mudanças (`git commit -m 'feat: adiciona NovaFeature'`)
4. Push para a branch (`git push origin feature/NovaFeature`)
5. Abra um Pull Request

### Convenção de Commits
Seguimos [Conventional Commits](https://www.conventionalcommits.org/):
- `feat:` - Nova funcionalidade
- `fix:` - Correção de bug
- `docs:` - Documentação
- `style:` - Formatação
- `refactor:` - Refatoração
- `test:` - Testes
- `chore:` - Manutenção

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👤 Autor

**Seu Nome**
- GitHub: [@seu-usuario](https://github.com/seu-usuario)
- LinkedIn: [Seu Nome](https://linkedin.com/in/seu-perfil)

---

## 🙏 Agradecimentos

- [MyAnimeList](https://myanimelist.net) - API de dados
- [Laravel](https://laravel.com) - Framework
- [Phosphor Icons](https://phosphoricons.com) - Iconografia
- [Google Fonts](https://fonts.google.com) - Tipografia

---

## 📸 Screenshots

### Dashboard
![Dashboard](docs/dashboard.png)

### Filtros
![Filtros](docs/filters.png)

### Modal de Detalhes
![Modal](docs/modal.png)

### Modo Streamer
![Streamer Mode](docs/streamer.png)

---

**Desenvolvido com ❤️ e ☕**
