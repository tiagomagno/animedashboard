<?php

/**
 * Teste simples da API MyAnimeList
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
echo "  TESTE DA API MYANIMELIST\n";
echo "═══════════════════════════════════════════════════════════\n\n";

if (!$clientId || $clientId === 'your_client_id_here') {
    echo "✗ Client ID não configurado!\n";
    echo "  Configure MAL_CLIENT_ID no arquivo .env\n\n";
    exit(1);
}

echo "✓ Client ID encontrado: " . substr($clientId, 0, 10) . "...\n\n";

// Testar conexão com a API
echo "Testando conexão com a API...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.myanimelist.net/v2/anime/1?fields=id,title');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-MAL-CLIENT-ID: ' . $clientId
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✓ Conexão estabelecida com sucesso!\n";
    echo "  Anime de teste: " . ($data['title'] ?? 'N/A') . "\n\n";
    
    // Testar endpoint de temporada
    echo "Testando endpoint de temporada (Winter 2024)...\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.myanimelist.net/v2/anime/season/2024/winter?fields=id,title,mean&limit=3');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-MAL-CLIENT-ID: ' . $clientId
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✓ Endpoint de temporada funcionando!\n";
        echo "  Animes encontrados: " . count($data['data']) . "\n\n";
        
        if (isset($data['data']) && count($data['data']) > 0) {
            echo "Primeiros 3 animes:\n";
            foreach ($data['data'] as $anime) {
                $node = $anime['node'];
                echo "  • " . $node['title'] . " (Score: " . ($node['mean'] ?? 'N/A') . ")\n";
            }
        }
        
        echo "\n";
        echo "╔════════════════════════════════════════════════════════╗\n";
        echo "║  ✓ VALIDAÇÃO CONCLUÍDA COM SUCESSO!                   ║\n";
        echo "║  Você pode começar a importar temporadas!             ║\n";
        echo "╚════════════════════════════════════════════════════════╝\n\n";
        
    } else {
        echo "✗ Erro ao buscar temporada (HTTP $httpCode)\n";
        echo "  Response: $response\n\n";
        exit(1);
    }
    
} else {
    echo "✗ Falha na conexão (HTTP $httpCode)\n";
    echo "  Response: $response\n\n";
    exit(1);
}
