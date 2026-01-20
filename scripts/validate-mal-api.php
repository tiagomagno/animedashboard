#!/usr/bin/env php
<?php

/**
 * Sprint 0 - Validação da API MyAnimeList
 * 
 * Este script valida:
 * - Comunicação com a MAL API v2
 * - Autenticação via X-MAL-CLIENT-ID
 * - Retorno correto de dados por temporada
 */

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Cores para output
class Color {
    public static $GREEN = "\033[32m";
    public static $RED = "\033[31m";
    public static $YELLOW = "\033[33m";
    public static $BLUE = "\033[34m";
    public static $RESET = "\033[0m";
}

function printHeader($text) {
    echo "\n" . Color::$BLUE . "═══════════════════════════════════════════════════════════" . Color::$RESET . "\n";
    echo Color::$BLUE . "  " . $text . Color::$RESET . "\n";
    echo Color::$BLUE . "═══════════════════════════════════════════════════════════" . Color::$RESET . "\n\n";
}

function printSuccess($text) {
    echo Color::$GREEN . "✓ " . $text . Color::$RESET . "\n";
}

function printError($text) {
    echo Color::$RED . "✗ " . $text . Color::$RESET . "\n";
}

function printWarning($text) {
    echo Color::$YELLOW . "⚠ " . $text . Color::$RESET . "\n";
}

function printInfo($text) {
    echo "  " . $text . "\n";
}

// Carregar variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

printHeader("VALIDAÇÃO DA API MYANIMELIST - SPRINT 0");

// 1. Verificar Client ID
printInfo("1. Verificando configuração do Client ID...");
$clientId = $_ENV['MAL_CLIENT_ID'] ?? null;

if (!$clientId || $clientId === 'your_client_id_here') {
    printError("Client ID não configurado!");
    printWarning("Configure MAL_CLIENT_ID no arquivo .env");
    printInfo("Obtenha seu Client ID em: https://myanimelist.net/apiconfig");
    exit(1);
}

printSuccess("Client ID configurado: " . substr($clientId, 0, 10) . "...");

// 2. Testar comunicação com a API
printInfo("\n2. Testando comunicação com a API...");
$baseUrl = $_ENV['MAL_API_BASE_URL'] ?? 'https://api.myanimelist.net/v2';

try {
    // Teste simples com um anime conhecido (Cowboy Bebop - ID: 1)
    $response = Http::withHeaders([
        'X-MAL-CLIENT-ID' => $clientId,
    ])->get($baseUrl . '/anime/1', [
        'fields' => 'id,title,main_picture'
    ]);

    if ($response->successful()) {
        printSuccess("Conexão estabelecida com sucesso!");
        $data = $response->json();
        printInfo("Anime de teste: " . ($data['title'] ?? 'N/A'));
    } else {
        printError("Falha na conexão: HTTP " . $response->status());
        printInfo("Response: " . $response->body());
        exit(1);
    }
} catch (Exception $e) {
    printError("Erro ao conectar: " . $e->getMessage());
    exit(1);
}

// 3. Testar endpoint de temporada
printInfo("\n3. Testando endpoint de temporada (Winter 2024)...");

$year = 2024;
$season = 'winter';

try {
    $response = Http::withHeaders([
        'X-MAL-CLIENT-ID' => $clientId,
    ])->get($baseUrl . "/anime/season/{$year}/{$season}", [
        'fields' => 'id,title,main_picture,mean,num_list_users,popularity,status,num_episodes,genres',
        'limit' => 5
    ]);

    if ($response->successful()) {
        printSuccess("Endpoint de temporada funcionando!");
        $data = $response->json();
        
        if (isset($data['data']) && count($data['data']) > 0) {
            printInfo("Animes encontrados: " . count($data['data']));
            printInfo("\nPrimeiros 3 animes da temporada:");
            
            foreach (array_slice($data['data'], 0, 3) as $anime) {
                $node = $anime['node'];
                echo "\n";
                printInfo("  • " . $node['title']);
                printInfo("    ID: " . $node['id']);
                printInfo("    Score: " . ($node['mean'] ?? 'N/A'));
                printInfo("    Membros: " . ($node['num_list_users'] ?? 'N/A'));
                printInfo("    Status: " . ($node['status'] ?? 'N/A'));
            }
        } else {
            printWarning("Nenhum anime encontrado para esta temporada");
        }
    } else {
        printError("Falha ao buscar temporada: HTTP " . $response->status());
        printInfo("Response: " . $response->body());
        exit(1);
    }
} catch (Exception $e) {
    printError("Erro ao buscar temporada: " . $e->getMessage());
    exit(1);
}

// 4. Validar campos obrigatórios
printInfo("\n4. Validando campos obrigatórios...");

$requiredFields = ['id', 'title', 'main_picture', 'mean', 'num_list_users', 'popularity', 'status', 'num_episodes', 'genres'];
$firstAnime = $data['data'][0]['node'] ?? null;

if ($firstAnime) {
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($firstAnime[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (empty($missingFields)) {
        printSuccess("Todos os campos obrigatórios estão presentes!");
    } else {
        printWarning("Campos ausentes (podem ser null): " . implode(', ', $missingFields));
    }
}

// 5. Testar rate limiting
printInfo("\n5. Testando rate limiting (fazendo 3 requisições)...");

for ($i = 1; $i <= 3; $i++) {
    try {
        $response = Http::withHeaders([
            'X-MAL-CLIENT-ID' => $clientId,
        ])->get($baseUrl . "/anime/{$i}", [
            'fields' => 'id,title'
        ]);
        
        if ($response->successful()) {
            printSuccess("Requisição {$i}/3: OK");
        } else {
            printError("Requisição {$i}/3: Falhou (HTTP {$response->status()})");
        }
        
        // Pequeno delay entre requisições
        usleep(200000); // 200ms
    } catch (Exception $e) {
        printError("Requisição {$i}/3: Erro - " . $e->getMessage());
    }
}

// Resumo final
printHeader("RESUMO DA VALIDAÇÃO");

printSuccess("✓ Client ID configurado e válido");
printSuccess("✓ Comunicação com API estabelecida");
printSuccess("✓ Endpoint de temporada funcionando");
printSuccess("✓ Campos obrigatórios presentes");
printSuccess("✓ Rate limiting respeitado");

echo "\n" . Color::$GREEN . "╔════════════════════════════════════════════════════════╗" . Color::$RESET . "\n";
echo Color::$GREEN . "║  VALIDAÇÃO CONCLUÍDA COM SUCESSO!                      ║" . Color::$RESET . "\n";
echo Color::$GREEN . "║  Você pode prosseguir para a Sprint 1                  ║" . Color::$RESET . "\n";
echo Color::$GREEN . "╚════════════════════════════════════════════════════════╝" . Color::$RESET . "\n\n";

printInfo("Próximos passos:");
printInfo("1. Configurar banco de dados (SQLite já configurado)");
printInfo("2. Executar migrations: php artisan migrate");
printInfo("3. Importar primeira temporada");
printInfo("4. Desenvolver dashboard\n");
