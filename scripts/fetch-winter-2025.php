<?php

/**
 * Buscar animes da temporada Winter 2025
 * Mostra apenas os nomes para teste
 */

// Ler o Client ID do .env
$envFile = __DIR__ . '/../.env';
$clientId = null;

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'MAL_CLIENT_ID=') === 0) {
            $clientId = trim(str_replace('MAL_CLIENT_ID=', '', $line));
            break;
        }
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  ANIMES DA TEMPORADA - WINTER 2025\n";
echo "═══════════════════════════════════════════════════════════\n\n";

if (!$clientId || $clientId === 'your_client_id_here') {
    echo "✗ Client ID não configurado!\n\n";
    exit(1);
}

$allAnimes = [];
$offset = 0;
$limit = 100;
$hasMore = true;
$page = 1;

echo "Buscando animes da API MyAnimeList...\n\n";

while ($hasMore) {
    echo "Página $page (offset: $offset)... ";
    
    $ch = curl_init();
    $url = "https://api.myanimelist.net/v2/anime/season/2025/winter?fields=id,title&limit=$limit&offset=$offset";
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-MAL-CLIENT-ID: ' . $clientId
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "✗ Erro: $error\n";
        break;
    }
    
    if ($httpCode !== 200) {
        echo "✗ HTTP $httpCode\n";
        if ($httpCode === 404) {
            echo "\nNenhum anime encontrado para Winter 2025.\n";
        }
        break;
    }
    
    $data = json_decode($response, true);
    
    if (!isset($data['data']) || empty($data['data'])) {
        echo "Sem mais dados.\n";
        break;
    }
    
    $count = count($data['data']);
    echo "✓ $count animes encontrados\n";
    
    foreach ($data['data'] as $anime) {
        $allAnimes[] = [
            'id' => $anime['node']['id'],
            'title' => $anime['node']['title']
        ];
    }
    
    // Verificar se há mais páginas
    $hasMore = isset($data['paging']['next']);
    $offset += $limit;
    $page++;
    
    // Delay para respeitar rate limiting
    if ($hasMore) {
        usleep(250000); // 250ms
    }
    
    // Limite de segurança
    if ($offset > 1000) {
        echo "\nLimite de segurança atingido (1000 animes).\n";
        break;
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  RESULTADO\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "Total de animes encontrados: " . count($allAnimes) . "\n\n";

if (count($allAnimes) > 0) {
    echo "Lista de animes:\n";
    echo "───────────────────────────────────────────────────────────\n\n";
    
    foreach ($allAnimes as $index => $anime) {
        $number = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
        echo "$number. {$anime['title']}\n";
    }
    
    echo "\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo "✓ Busca concluída com sucesso!\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
} else {
    echo "Nenhum anime encontrado para esta temporada.\n\n";
}
