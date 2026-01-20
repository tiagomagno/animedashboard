# 🚀 Guia de Instalação - Anime Dashboard

## ⚠️ Problema: Driver SQLite não encontrado

Se você recebeu o erro `could not find driver (Connection: sqlite)`, siga as instruções abaixo:

## Solução 1: Habilitar SQLite no PHP (Recomendado)

### Windows

1. **Localize o arquivo `php.ini`**
   ```bash
   php --ini
   ```
   
2. **Abra o arquivo `php.ini` em um editor de texto**

3. **Procure e descomente as seguintes linhas** (remova o `;` no início):
   ```ini
   ;extension=pdo_sqlite
   ;extension=sqlite3
   ```
   
   Deve ficar assim:
   ```ini
   extension=pdo_sqlite
   extension=sqlite3
   ```

4. **Salve o arquivo e reinicie o terminal**

5. **Verifique se o SQLite está habilitado**:
   ```bash
   php -m | findstr sqlite
   ```
   
   Deve mostrar:
   ```
   pdo_sqlite
   sqlite3
   ```

6. **Execute as migrations novamente**:
   ```bash
   cd c:\Projects\animedashboard
   php artisan migrate
   ```

## Solução 2: Usar MySQL (Alternativa)

Se você preferir usar MySQL ou já tem instalado:

### 1. Crie o banco de dados

```sql
CREATE DATABASE animedashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Configure o `.env`

Edite o arquivo `.env` e altere:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=animedashboard
DB_USERNAME=root
DB_PASSWORD=sua_senha_aqui
```

### 3. Execute as migrations

```bash
php artisan migrate
```

## Solução 3: Instalar PHP com SQLite (XAMPP/Laragon)

Se você usa XAMPP ou Laragon, o SQLite já deve estar incluído. Certifique-se de:

1. Usar o PHP do XAMPP/Laragon
2. Verificar se as extensões estão habilitadas no `php.ini`

## Verificação Final

Após configurar, execute:

```bash
# Verificar extensões PHP
php -m

# Executar migrations
php artisan migrate

# Iniciar servidor
php artisan serve
```

## Próximos Passos

Após resolver o problema do banco de dados:

1. **Configure o Client ID do MyAnimeList**
   - Edite `.env` e adicione `MAL_CLIENT_ID=seu_client_id`
   - Obtenha em: https://myanimelist.net/apiconfig

2. **Valide a API**
   ```bash
   php scripts/validate-mal-api.php
   ```

3. **Acesse a aplicação**
   ```bash
   php artisan serve
   ```
   
   Abra: http://localhost:8000

## Precisa de Ajuda?

- Verifique a versão do PHP: `php -v` (requer 8.2+)
- Liste extensões: `php -m`
- Verifique o php.ini: `php --ini`
